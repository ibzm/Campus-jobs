<?php

namespace App\Http\Controllers;
use App\Models\HourRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Job;
use Carbon\Carbon;
use App\Notifications\TimesheetFlagged;
use App\Notifications\HourRequestDenied;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RecruiterController extends Controller
{
    public function create()
    {
        $recruiterId = Auth::id();
    
        $students = User::join('job_assignments', 'users.id', '=', 'job_assignments.student_id')
            ->join('jobs', 'job_assignments.job_id', '=', 'jobs.id')
            ->where('jobs.recruiter_id', $recruiterId)
            ->select('users.*')
            ->distinct()
            ->get();
    
        $jobs = Job::where('recruiter_id', $recruiterId)->get();
    
        $hourRequests = HourRequest::whereIn('student_id', $students->pluck('id'))->get();
        $studentJobs = [];
        foreach ($students as $student) {
            $assignedJobs = \DB::table('job_assignments')
                ->join('jobs', 'job_assignments.job_id', '=', 'jobs.id')
                ->where('job_assignments.student_id', $student->id)
                ->where('jobs.recruiter_id', $recruiterId)
                ->select('jobs.id', 'jobs.title')
                ->get();
            $studentJobs[$student->id] = $assignedJobs;
        }
    
        return view('recruiter.create', compact('students', 'jobs', 'hourRequests', 'studentJobs'));
    }
    
    

    public function getStudentsUnderRecruiter()
    {
        $recruiterId = Auth::id();

        $students = User::whereHas('assignedJobs', function ($query) use ($recruiterId) {
            $query->whereHas('recruiter', function ($subQuery) use ($recruiterId) {
                $subQuery->where('recruiter_id', $recruiterId);
            });
        })->get();

        return response()->json($students);
    }

    public function index(Request $request)
    {
        $recruiterId = auth()->user()->id;

        $flaggedTimesheets = Timesheet::whereHas('user.assignedJobs.recruiter', function ($query) use ($recruiterId) {
            $query->where('id', $recruiterId);
        })->where('flagged', true)->count();

        $pendingRequests = HourRequest::where('recruiter_id', $recruiterId)
            ->where('status', 'pending')
            ->count();

        $students = User::whereHas('assignedJobs', function ($query) use ($recruiterId) {
            $query->whereHas('recruiter', function ($subQuery) use ($recruiterId) {
                $subQuery->where('recruiter_id', $recruiterId);
            });
        })->with(['timesheets'])->get();

        $lowHourStudents = $students->filter(fn($student) => $student->remaining_hours <= 5)->count();

        $nearLimitStudents = 0;
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        foreach ($students as $student) {
            $weeklyTotal = $student->timesheets
                ->whereBetween('shift_start', [$weekStart, $weekEnd])
                ->sum('hours_requested');
            if ($weeklyTotal >= 12) {
                $nearLimitStudents++;
            }
        }

        $recentTimesheets = Timesheet::whereHas('user.assignedJobs.recruiter', function ($query) use ($recruiterId) {
            $query->where('id', $recruiterId);
        })->orderBy('created_at', 'desc')
            ->take(5)
            ->with('user')
            ->get();

        return view('recruiter.index', compact(
            'flaggedTimesheets',
            'pendingRequests',
            'lowHourStudents',
            'nearLimitStudents',
            'recentTimesheets'
        ));
    }

    public function myStudents()
    {
        $recruiterId = Auth::id();

        $jobs = Job::with('students.timesheets')
            ->where('recruiter_id', $recruiterId)
            ->get();

        $students = $jobs->flatMap->students->unique('id');

        $students = $students->map(function ($student) {
            $student->has_flagged_timesheet = $student->timesheets->contains('flagged', true);
            return $student;
        });


        $students = $students->sortByDesc('has_flagged_timesheet');

        return view('recruiter.students', compact('students'));
    }




    public function store(Request $request)
    {
        try {
            $request->validate([
                'student_id'      => 'required|exists:users,id',
                'job_id'          => 'required|exists:jobs,id',
                'shift_start'     => 'required|date',
                'shift_end'       => 'required|date|after:shift_start',
                'hour_request_id' => 'nullable|exists:hour_requests,id', 
            ]);
    
            Log::info('Store Method Data:', $request->all());
    
            $recruiterId = Auth::id();
            $job = Job::where('id', $request->job_id)
                ->where('recruiter_id', $recruiterId)
                ->first();
    
            if (!$job) {
                Log::error("Invalid job or unauthorized recruiter. Job ID: " . $request->job_id);
                return back()->with('error', 'Invalid job or unauthorized recruiter.');
            }
    
            $isStudentAssigned = DB::table('job_assignments')
                ->where('job_id', $request->job_id)
                ->where('student_id', $request->student_id)
                ->exists();
    
            if (!$isStudentAssigned) {
                Log::error("This student is not assigned to this job. Student ID: " . $request->student_id . " Job ID: " . $request->job_id);
                return back()->with('error', 'This student is not assigned to this job.');
            }
    
            $student = User::find($request->student_id);
            $shiftStart = Carbon::parse($request->shift_start);
            $shiftEnd   = Carbon::parse($request->shift_end);
    
            $shiftDuration = $shiftStart->diffInMinutes($shiftEnd) / 60;
            if ($shiftDuration > 20) {
                return back()->with('error', 'Shift length cannot exceed 20 hours.');
            }
    
            $hoursWorked    = ceil($shiftDuration);
            $hoursRequested = $hoursWorked;
    
            $flagged = false;
    
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd   = Carbon::now()->endOfWeek();
            $weeklyHours = Timesheet::where('user_id', $student->id)
                ->whereBetween('shift_start', [$weekStart, $weekEnd])
                ->sum('hours_requested');
            $totalRequestedHours = $weeklyHours + $hoursRequested;
            if ($totalRequestedHours > 15) {
                $flagged = true;
                Log::warning("Visa limit exceeded. Flagged for review. Student ID: " . $student->id);
            }
    
            $newRemainingHours = $student->remaining_hours - $hoursRequested;
            if ($newRemainingHours < 5) {
                $flagged = true;
                Log::warning("Remaining hours will drop below 5. Student ID: " . $student->id . " has {$student->remaining_hours} remaining hours; after the request: {$newRemainingHours}.");
            }
            
            $providedHourRequestId = $request->input('hour_request_id');
            if ($providedHourRequestId) {
                $hourRequest = HourRequest::find($providedHourRequestId);
                if (!$hourRequest || $hourRequest->student_id != $student->id) {
                    return back()->with('error', 'Invalid hour request provided for this student.');
                }
                if (isset($hourRequest->is_dummy) && $hourRequest->is_dummy) {
                    $flagged = true;
                }
            } else {
                $shiftDate = $shiftStart->toDateString();
                $hourRequest = HourRequest::create([
                    'student_id'      => $student->id,
                    'recruiter_id'    => $recruiterId,
                    'requested_hours' => $hoursRequested,
                    'status'          => 'pending', 
                    'is_dummy'        => true,       
                    'requested_date'  => $shiftDate,
                    'start_time'      => $shiftStart->format('H:i:s'),
                    'end_time'        => $shiftEnd->format('H:i:s'),
                ]);
                $flagged = true;
            }
    
            $timesheet = Timesheet::create([
                'user_id'         => $request->student_id,
                'job_id'          => $request->job_id,
                'shift_start'     => $request->shift_start,
                'shift_end'       => $request->shift_end,
                'status'          => 'pending',
                'hours_requested' => $hoursRequested,
                'flagged'         => $flagged,
                'hour_request_id' => $hourRequest->id,
            ]);
    
            if ($flagged) {
                $recruiter = User::find($recruiterId);
                $recruiter->notify(new TimesheetFlagged($timesheet));
            }
    
            Log::info("Timesheet created successfully. Student ID: " . $request->student_id);
            return redirect()->route('recruiter.index')
                ->with('success', 'Timesheet submitted successfully' . ($flagged ? ' and flagged for review due to weekly limits or low remaining hours.' : ''));
        } catch (\Exception $e) {
            Log::error('Error creating timesheet: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while submitting the timesheet.');
        }
    }
    
    public function showHourRequestForm()
    {
        $recruiterId = Auth::id();

        $students = User::join('job_assignments', 'users.id', '=', 'job_assignments.student_id')
            ->join('jobs', 'job_assignments.job_id', '=', 'jobs.id')
            ->where('jobs.recruiter_id', $recruiterId)
            ->select('users.*')
            ->distinct()
            ->get();

        return view('recruiter.hour_request', compact('students'));
    }

    public function submitHourRequest(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence_weeks' => 'nullable|integer|min:1',
            'comment' => 'nullable|string|max:1000',
        ]);
    
        $student = User::findOrFail($request->student_id);
    
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $hoursRequested = ceil($startTime->diffInMinutes($endTime) / 60);
        $weeks = $request->recurrence_weeks ?? 1;
    
        for ($i = 0; $i < $weeks; $i++) {
            $requestDate = Carbon::parse($request->start_date)->addWeeks($i);
    
            $status = 'pending';
    
            $hourRequest = HourRequest::create([
                'student_id' => $student->id,
                'recruiter_id' => auth()->id(),
                'requested_hours' => $hoursRequested,
                'status' => $status,
                'requested_date' => $requestDate,
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'comment' => $request->input('comment'),
            ]);
    
        }
    
        return redirect()->back()->with('success', 'Hour request(s) submitted and are pending approval.');
    }
    public function exportHourRequests(Request $request)
    {
        $recruiterId = Auth::id();


        $hourRequests = HourRequest::where('recruiter_id', $recruiterId)
            ->get(['student_id', 'requested_hours', 'status', 'requested_date', 'start_time', 'end_time', 'comment']);


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Student ID');
        $sheet->setCellValue('B1', 'Requested Hours');
        $sheet->setCellValue('C1', 'Status');
        $sheet->setCellValue('D1', 'Requested Date');
        $sheet->setCellValue('E1', 'Start Time');
        $sheet->setCellValue('F1', 'End Time');
        $sheet->setCellValue('G1', 'Comment');

        $row = 2;
        foreach ($hourRequests as $hourRequest) {
            $sheet->setCellValue('A' . $row, $hourRequest->student_id);
            $sheet->setCellValue('B' . $row, $hourRequest->requested_hours);
            $sheet->setCellValue('C' . $row, $hourRequest->status);
            $sheet->setCellValue('D' . $row, Carbon::parse($hourRequest->requested_date)->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $hourRequest->start_time);
            $sheet->setCellValue('F' . $row, $hourRequest->end_time);
            $sheet->setCellValue('G' . $row, $hourRequest->comment);
            $row++;
        }

        $fileName = 'hour_requests_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }


}