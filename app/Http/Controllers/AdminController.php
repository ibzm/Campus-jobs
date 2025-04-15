<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\HourRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\AuditLog;

class AdminController extends Controller
{
 
    public function index()
    {
        $timesheets = Timesheet::with(['job', 'user'])->orderBy('created_at', 'desc')->get();
        
        $hourRequests = HourRequest::with(['student', 'recruiter'])->orderBy('created_at', 'desc')->get();
        
        $approvedTimesheets = Timesheet::where('status', 'approved')->count();
        $rejectedTimesheets = Timesheet::where('status', 'rejected')->count();
        $pendingTimesheets  = Timesheet::where('status', 'pending')->count();
        
        $approvedHourRequests = HourRequest::where('status', 'approved')->count();
        $rejectedHourRequests   = HourRequest::where('status', 'rejected')->count();
        $pendingHourRequests  = HourRequest::where('status', 'pending')->count();
        
        return view('admin.dashboard', compact(
            'timesheets', 
            'hourRequests',
            'approvedTimesheets',
            'rejectedTimesheets',
            'pendingTimesheets',
            'approvedHourRequests',
            'rejectedHourRequests',
            'pendingHourRequests'
        ));
    }
    public function auditLogs()
    {
        $auditLogs = \App\Models\AuditLog::orderBy('created_at', 'desc')->get();
        return view('admin.audit_logs', compact('auditLogs'));
    }
    

    public function editTimesheet($id)
    {
        $timesheet = Timesheet::with(['job', 'user'])->findOrFail($id);
        return view('admin.edit_timesheet', compact('timesheet'));
    }

    public function updateTimesheet(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:pending,approved,rejected',
            'override_message' => 'nullable|string|max:1000',
            'hours_requested'  => 'required|integer|min:0',
        ]);
    
        $timesheet = Timesheet::findOrFail($id);
        $oldStatus = $timesheet->status;
        $oldOverride = $timesheet->override_message;
        $oldHours = $timesheet->hours_requested;
    
        $timesheet->status = $request->input('status');
        $timesheet->hours_requested = $request->input('hours_requested');
    
        if ($request->has('override_message')) {
            $timesheet->override_message = $request->input('override_message');
        }
        $timesheet->save();
    
        $changes = [
            'old_status' => $oldStatus,
            'new_status' => $timesheet->status,
            'old_override_message' => $oldOverride,
            'new_override_message' => $timesheet->override_message,
            'old_hours_requested' => $oldHours,
            'new_hours_requested' => $timesheet->hours_requested,
        ];
    
        \App\Models\AuditLog::create([
            'record_type' => 'timesheet',
            'record_id'   => $timesheet->id,
            'user_id'     => auth()->id(),
            'changes'     => json_encode($changes)
        ]);
    
        return redirect()->route('admin.dashboard')->with('success', 'Timesheet updated successfully.');
    }
    
    


    public function editHourRequest($id)
    {
        $hourRequest = HourRequest::with(['student', 'recruiter'])->findOrFail($id);
        return view('admin.edit_hour_request', compact('hourRequest'));
    }

    public function updateHourRequest(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected',
        'requested_hours' => 'required|integer|min:0',
        'reason' => 'nullable|string|max:1000'
    ]);

    $hourRequest = HourRequest::findOrFail($id);
    $oldStatus = $hourRequest->status;
    $oldHours = $hourRequest->requested_hours;
    $oldReason = $hourRequest->reason;

    $newStatus = $request->input('status');
    $newHours = $request->input('requested_hours');
    $newReason = $request->input('reason'); 

    $hourRequest->status = $newStatus;
    $hourRequest->requested_hours = $newHours;
    $hourRequest->reason = $newReason;
    $hourRequest->save();
    $hourRequest->refresh();

    $student = $hourRequest->student;


    if ($oldStatus !== 'approved' && $newStatus === 'approved') {
        $student->remaining_hours -= $newHours;
    } elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
        $student->remaining_hours += $oldHours;
    } elseif ($oldStatus === 'approved' && $newStatus === 'approved') {
        $student->remaining_hours += ($oldHours - $newHours);
    }
    $student->save();

    $changes = [
        'old_status' => $oldStatus,
        'new_status' => $newStatus,
        'old_requested_hours' => $oldHours,
        'new_requested_hours' => $newHours,
        'old_reason' => $oldReason,
        'new_reason' => $newReason,
    ];
    \App\Models\AuditLog::create([
        'record_type' => 'hour_request',
        'record_id'   => $hourRequest->id,
        'user_id'     => auth()->id(),
        'changes'     => json_encode($changes)
    ]);

    return redirect()->route('admin.dashboard')->with('success', 'Hour request updated successfully.');
}


    

    public function exportReport(Request $request)
    {
        $timesheets = Timesheet::with(['job', 'user'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Timesheet ID');
        $sheet->setCellValue('B1', 'Student');
        $sheet->setCellValue('C1', 'Job');
        $sheet->setCellValue('D1', 'Hours Worked');
        $sheet->setCellValue('E1', 'Shift Start');
        $sheet->setCellValue('F1', 'Shift End');
        $sheet->setCellValue('G1', 'Status');
        
        $row = 2;
        foreach($timesheets as $ts) {
            $sheet->setCellValue('A' . $row, $ts->id);
            $sheet->setCellValue('B' . $row, $ts->user->first_name . ' ' . $ts->user->second_name);
            $sheet->setCellValue('C' . $row, $ts->job->title);
            $sheet->setCellValue('D' . $row, $ts->hours_requested);
            $sheet->setCellValue('E' . $row, $ts->shift_start);
            $sheet->setCellValue('F' . $row, $ts->shift_end);
            $sheet->setCellValue('G' . $row, ucfirst($ts->status));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'timesheets_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $writer->save($filename);
        
        return response()->download($filename)->deleteFileAfterSend(true);
    }
    public function notifications()
{
    $admin = auth()->user();
    $notifications = $admin->notifications; 
    return view('admin.notifications', compact('notifications'));
}
public function exportHourRequests(Request $request)
{
    $hourRequests = HourRequest::with(['student', 'recruiter'])->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Hour Request ID');
    $sheet->setCellValue('B1', 'Student');
    $sheet->setCellValue('C1', 'Recruiter');
    $sheet->setCellValue('D1', 'Requested Hours');
    $sheet->setCellValue('E1', 'Status');
    $sheet->setCellValue('F1', 'Requested Date');
    $sheet->setCellValue('G1', 'Start Time');
    $sheet->setCellValue('H1', 'End Time');
    $sheet->setCellValue('I1', 'Reason');
    $sheet->setCellValue('J1', 'Comment');

    $row = 2;
    foreach ($hourRequests as $hr) {
        $sheet->setCellValue('A' . $row, $hr->id);
        $sheet->setCellValue('B' . $row, $hr->student->first_name . ' ' . $hr->student->second_name);
        $sheet->setCellValue('C' . $row, $hr->recruiter->first_name . ' ' . $hr->recruiter->second_name);
        $sheet->setCellValue('D' . $row, $hr->requested_hours);
        $sheet->setCellValue('E' . $row, ucfirst($hr->status));
        $sheet->setCellValue('F' . $row, $hr->requested_date);
        $sheet->setCellValue('G' . $row, $hr->start_time);
        $sheet->setCellValue('H' . $row, $hr->end_time);
        $sheet->setCellValue('I' . $row, $hr->reason);
        $sheet->setCellValue('J' . $row, $hr->comment);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'hour_requests_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
    $writer->save($filename);

    return response()->download($filename)->deleteFileAfterSend(true);
}

}
