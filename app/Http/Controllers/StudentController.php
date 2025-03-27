<?php

namespace App\Http\Controllers;


use App\Models\Item;
use App\Models\Job;
use App\Models\Timesheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StudentController extends Controller
{
    public function index()
    {
        $studentId = Auth::id(); 
        
        $student = User::findOrFail($studentId);

        $jobs = Job::join('job_assignments', 'jobs.id', '=', 'job_assignments.job_id')
                    ->where('job_assignments.student_id', $studentId)
                    ->get();

        $timesheets = Timesheet::join('jobs', 'timesheet.job_id', '=', 'jobs.id')
                    ->where('timesheet.user_id', $studentId)
                    ->get();

        return view('students.index', compact('student', 'jobs', 'timesheets'));
    }
}