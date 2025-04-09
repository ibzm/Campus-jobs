<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Job;
use App\Models\Timesheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

class StudentController extends Controller
{
   
    public function index()
    {
        $studentId = Auth::id(); 
        
        $student = User::findOrFail($studentId);
    
        $jobs = Job::join('job_assignments', 'jobs.id', '=', 'job_assignments.job_id')
                    ->where('job_assignments.student_id', $studentId)
                    ->get();
    
        $timesheets = Timesheet::join('jobs', 'timesheets.job_id', '=', 'jobs.id')
                               ->where('timesheets.user_id', $studentId)
                               ->get();
    
        if ($timesheets->isEmpty()) {
            $timesheets = collect(); 
        }
    
        return view('students.index', compact('student', 'jobs', 'timesheets'));
    }
    

  
    public function approveTimesheet($timesheetId)
    {
     
        $timesheet = Timesheet::findOrFail($timesheetId);
        
    
        if ($timesheet->user_id !== Auth::id()) {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized action.');
        }

      
        $timesheet->approved = 1;
        $timesheet->save();

        return redirect()->route('student.dashboard')->with('success', 'Timesheet approved successfully.');
    }


    public function showTimesheet($timesheetId)
    {
        $timesheet = Timesheet::with('job')->findOrFail($timesheetId);
    
        if ($timesheet->user_id !== Auth::id()) {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized action.');
        }
    
        
        return view('students.timesheet_details', compact('timesheet'));
    }

public function showApprovedTimesheets()
{
    $studentId = Auth::id();

 
    $timesheets = Timesheet::join('jobs', 'timesheets.job_id', '=', 'jobs.id')
                           ->where('timesheets.user_id', $studentId)
                           ->where('timesheets.approved', 1)
                           ->get();

    return view('students.approvedTimesheets', compact('timesheets'));
}

}