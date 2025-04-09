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

        return view('recruiter.create', compact('students', 'jobs'));
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
                'student_id' => 'required|exists:users,id',
                'job_id' => 'required|exists:jobs,id',
                'shift_start' => 'required|date',
                'shift_end' => 'required|date|after:shift_start',
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
            $shiftEnd = Carbon::parse($request->shift_end);
    
            $hoursWorked = $shiftStart->diffInMinutes($shiftEnd) / 60;
            $hoursWorked = ceil($hoursWorked); 
            $hoursRequested = $hoursWorked;
    
            if ($student->remaining_hours < $hoursRequested) {
                Log::warning("Not enough remaining hours for student. Student ID: " . $request->student_id);
                return back()->with('error', 'Not enough remaining hours.');
            }
    
            $flagged = false;
    
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            $weeklyHours = Timesheet::where('user_id', $student->id)
                ->whereBetween('shift_start', [$weekStart, $weekEnd])
                ->sum('hours_requested');
    
            $totalRequestedHours = $weeklyHours + $hoursRequested;
    
            if ($totalRequestedHours > 15) {
                $flagged = true;
                Log::warning("Visa limit exceeded. Flagged for review. Student ID: " . $student->id);
            }
    
            if (($student->remaining_hours - $hoursRequested) <= 5) {
                $flagged = true;
                Log::warning("Remaining hours will be 5 or less after this timesheet. Flagged for review. Student ID: " . $student->id);
            }
            $timesheet = Timesheet::create([
                'user_id' => $request->student_id,
                'job_id' => $request->job_id,
                'shift_start' => $request->shift_start,
                'shift_end' => $request->shift_end,
                'approved' => false,  
                'hours_requested' => $hoursRequested,
                'flagged' => $flagged,
            ]);
    
            $student->remaining_hours -= $hoursRequested;
            $student->save();
    
            if ($flagged) {
                $recruiter = User::find($recruiterId);
                $recruiter->notify(new TimesheetFlagged($timesheet));
            }
    
            Log::info("Timesheet created successfully. Student ID: " . $request->student_id);
            return redirect()->route('recruiter.index')->with('success', 'Timesheet submitted successfully' . ($flagged ? ' and flagged for review due to weekly limits or low remaining hours.' : ''));
    
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
    
        $wouldRemain = $student->remaining_hours - $hoursRequested;
        $isApprovable = $student->remaining_hours >= $hoursRequested && $wouldRemain > 5;
    
        $status = $isApprovable ? 'approved' : 'denied';
    
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
    
        if ($status === 'approved') {
            $student->remaining_hours -= $hoursRequested;
            $student->save();
        } else {
            $recruiter = auth()->user(); 
            $recruiter->notify(new HourRequestDenied($hourRequest));
        }
    }
    

    return redirect()->back()->with('success', 'Hour request(s) submitted!');
}
public function exportHourRequests(Request $request)
{
    $recruiterId = Auth::id();

   
    $hourRequests = HourRequest::where('recruiter_id', $recruiterId)
        ->get(['student_id', 'requested_hours', 'status', 'requested_date', 'start_time', 'end_time', 'comment']);
    
 
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers for the Excel file
    $sheet->setCellValue('A1', 'Student ID');
    $sheet->setCellValue('B1', 'Requested Hours');
    $sheet->setCellValue('C1', 'Status');
    $sheet->setCellValue('D1', 'Requested Date');
    $sheet->setCellValue('E1', 'Start Time');
    $sheet->setCellValue('F1', 'End Time');
    $sheet->setCellValue('G1', 'Comment');
    
    // Populate the spreadsheet with data
    $row = 2; // Start from row 2 to leave row 1 for headers
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

    // Set the file name
    $fileName = 'hour_requests_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

    // Write the file to the output
    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);

    // Return the file as a response for download
    return response()->download($fileName)->deleteFileAfterSend(true);
}


}    