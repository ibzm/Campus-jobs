<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Job;
use Carbon\Carbon;

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

        $timesheets = DB::table('timesheet')
            ->join('users', 'timesheet.user_id', '=', 'users.id')
            ->join('job_assignments', 'users.id', '=', 'job_assignments.student_id')
            ->join('jobs', 'job_assignments.job_id', '=', 'jobs.id')
            ->where('jobs.recruiter_id', $recruiterId)
            ->select('timesheet.*', 'users.first_name', 'users.email')
            ->get();

        return view('recruiter.index', compact('timesheets'));
    }

    public function myStudents()
    {
        $recruiterId = Auth::id(); 
    
        $students = User::join('job_assignments', 'users.id', '=', 'job_assignments.student_id')
            ->join('jobs', 'job_assignments.job_id', '=', 'jobs.id')
            ->where('jobs.recruiter_id', $recruiterId)
            ->select('users.*')
            ->distinct()
            ->get();
    
        return view('recruiter.students', compact('students'));
    }

    


    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:users,id',
        'job_id' => 'required|exists:jobs,id',
        'shift_start' => 'required|date',
        'shift_end' => 'required|date|after:shift_start',
    ]);

    $recruiterId = Auth::id();

    $job = Job::where('id', $request->job_id)
        ->where('recruiter_id', $recruiterId)
        ->first();

    if (!$job) {
        return back()->with('error', 'Invalid job or unauthorized recruiter.');
    }

    $isStudentAssigned = DB::table('job_assignments')
        ->where('job_id', $request->job_id)
        ->where('student_id', $request->student_id)
        ->exists();

    if (!$isStudentAssigned) {
        return back()->with('error', 'This student is not assigned to this job.');
    }

    $student = User::find($request->student_id);
    $shiftStart = Carbon::parse($request->shift_start);
    $shiftEnd = Carbon::parse($request->shift_end);

    $hoursWorked = $shiftStart->diffInHours($shiftEnd);
    if ($shiftStart->diffInMinutes($shiftEnd) % 60 > 0) {
        $hoursWorked++;
    }

    $hoursRequested = $hoursWorked;

    if ($student->remaining_hours < $hoursRequested) {
        return back()->with('error', 'Not enough remaining hours.');
    }

    Timesheet::create([
        'user_id' => $request->student_id,
        'job_id' => $request->job_id,
        'shift_start' => $request->shift_start,
        'shift_end' => $request->shift_end,
        'approved' => false,  
        'hours_requested' => $hoursRequested,  
    ]);

    $student->remaining_hours -= $hoursRequested;
    $student->save();

    return redirect()->route('recruiter.index')->with('success', 'Timesheet submitted successfully');
}

    
    
}
