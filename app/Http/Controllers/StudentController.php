<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Job;
use App\Models\Timesheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\HourRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\HourRequestApproved;
use App\Notifications\HourRequestDenied;

class StudentController extends Controller
{

    public function index()
{
    $studentId = Auth::id();
    $student = User::findOrFail($studentId);

    $jobs = Job::join('job_assignments', 'jobs.id', '=', 'job_assignments.job_id')
        ->join('users as recruiters', 'jobs.recruiter_id', '=', 'recruiters.id')
        ->where('job_assignments.student_id', $studentId)
        ->select(
            'jobs.*',
            \DB::raw("CONCAT(recruiters.first_name, ' ', recruiters.second_name) as recruiter_name")
        )
        ->get();

    $timesheets = Timesheet::with('job')
        ->where('user_id', $studentId)
        ->where('status', 'pending')
        ->get();

    $pendingHourRequests = HourRequest::where('student_id', $studentId)
        ->where('status', 'pending')
        ->get();

    return view('students.index', compact('student', 'jobs', 'timesheets', 'pendingHourRequests'));
}

    

public function processHourRequest(Request $request, $hourRequestId)
{
    $request->validate([
        'decision' => 'required|in:approve,reject',
    ]);

    $student = Auth::user();
    $hourRequest = HourRequest::where('id', $hourRequestId)->firstOrFail();
    if ($hourRequest->student_id != $student->id) {
        return redirect()->back()->with('error', 'Unauthorized action on this hour request.');
    }

    if ($hourRequest->status !== 'pending') {
        return redirect()->back()->with('error', 'This hour request has already been processed.');
    }

    if ($request->input('decision') === 'approve') {
        if ($student->remaining_hours < $hourRequest->requested_hours) {
            return redirect()->back()->with('error', 'Not enough remaining hours to approve this request.');
        }
        $student->remaining_hours -= $hourRequest->requested_hours;
        $student->save();

        $hourRequest->status = 'approved';
        $hourRequest->save();

        $student->notify(new HourRequestApproved($hourRequest));
        return redirect()->back()->with('success', 'Hour request approved and hours deducted.');
    } else if ($request->input('decision') === 'reject') {
        $hourRequest->status = 'rejected';
        $hourRequest->save();

        $student->notify(new HourRequestDenied($hourRequest));
        return redirect()->back()->with('success', 'Hour request rejected.');
    }

    return redirect()->back()->with('error', 'Invalid action.');
}

    public function history()
    {
        $studentId = Auth::id();
        $student = User::findOrFail($studentId);

        $allHourRequests = HourRequest::where('student_id', $studentId)->get();

        $allTimesheets = Timesheet::with('job')
            ->where('user_id', $studentId)
            ->get();

        return view('students.history', compact('student', 'allHourRequests', 'allTimesheets'));
    }

    public function processTimesheet(Request $request, $timesheetId)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
        ]);

        $student = Auth::user();
        $timesheet = Timesheet::findOrFail($timesheetId);


        if ($timesheet->user_id !== $student->id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Unauthorized action.');
        }

        if ($timesheet->status !== 'pending') {
            return redirect()->route('student.dashboard')
                ->with('error', 'This timesheet has already been processed.');
        }

        if ($request->input('decision') === 'approve') {
            $timesheet->status = 'approved';
            $message = 'Timesheet approved successfully.';
        } else {
            $timesheet->status = 'rejected';
            $message = 'Timesheet rejected.';
        }

        $timesheet->save();

        return redirect()->route('student.dashboard')->with('success', $message);
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
            ->where('timesheets.status', 'approved')
            ->get();

        return view('students.approvedTimesheets', compact('timesheets'));
    }


    public function upcomingShifts()
    {
        $studentId = Auth::id();
        $now = Carbon::now();

        $upcomingHourRequests = HourRequest::where('student_id', $studentId)
            ->where('status', 'approved')
            ->whereDate('requested_date', '>=', $now->toDateString())
            ->orderBy('requested_date')
            ->orderBy('start_time')
            ->get();

        return view('students.upcoming_shifts', compact('upcomingHourRequests'));
    }

    public function notifications()
    {
        $student = Auth::user();
    
        $notifications = $student->notifications; 
    
        return view('students.notifications', compact('notifications'));
    }
    

}