<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/recruiter', [RecruiterController::class, 'index'])
        ->name('recruiter.index');
    Route::get('/recruiter/create', [RecruiterController::class, 'create'])
        ->name('recruiter.create');

    Route::get('/hour-request', [RecruiterController::class, 'showHourRequestForm'])
        ->name('recruiter.hour-request');
    Route::post('/hour-request', [RecruiterController::class, 'submitHourRequest'])
        ->name('recruiter.hour-request.submit');

    Route::get('/recruiter/export-hour-requests', [RecruiterController::class, 'exportHourRequests'])->name('recruiter.exportHourRequests');

    Route::post('/recruiter', [RecruiterController::class, 'store'])
        ->name('recruiter.store');
        Route::get('/recruiter/students', [RecruiterController::class, 'myStudents'])->name('recruiter.students');
});

Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/timesheet/{id}/edit', [AdminController::class, 'editTimesheet'])->name('admin.edit_timesheet');
Route::put('/admin/timesheet/{id}', [AdminController::class, 'updateTimesheet'])->name('admin.updateTimesheet');
Route::get('/admin/hour-request/{id}/edit', [AdminController::class, 'editHourRequest'])->name('admin.editHourRequest');
Route::put('/admin/hour-request/{id}', [AdminController::class, 'updateHourRequest'])->name('admin.updateHourRequest');
Route::get('/admin/export-report', [AdminController::class, 'exportReport'])->name('admin.exportReport');
Route::get('/admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
Route::get('/admin/export-hour-requests', [AdminController::class, 'exportHourRequests'])->name('admin.exportHourRequests');
Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.auditLogs');


    

    Route::middleware('auth')->group(function () {
      
        Route::get('/students', [StudentController::class, 'index'])->name('student.index');
        Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
        Route::get('/timesheet/{timesheetId}', [StudentController::class, 'showTimesheet'])->name('student.showTimesheet');
        Route::post('/approve-timesheet/{timesheetId}/process', [StudentController::class, 'processTimesheet'])->name('student.processTimesheet');
        Route::get('/student/approved-timesheets', [StudentController::class, 'showApprovedTimesheets'])->name('student.approvedTimesheets');
        
        Route::get('/student/history', [StudentController::class, 'history'])->name('student.history');
        Route::get('/student/upcoming-shifts', [StudentController::class, 'upcomingShifts'])->name('student.upcomingShifts');
        Route::get('/student/notifications', [StudentController::class, 'notifications'])->name('student.notifications');
        

        Route::post('/student/hour-request/{hourRequestId}/process', [StudentController::class, 'processHourRequest'])->name('student.processHourRequest');
    });
    


require __DIR__ . '/auth.php';
