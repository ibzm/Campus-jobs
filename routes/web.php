<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/students', [StudentController::class, 'index'])
->name('/students');

Route::get('/students/show_timesheet', [StudentController::class, 'show_timesheet'])
->name('students.show_timesheet');

Route::get('/students/timesheet', [StudentController::class, 'create'])
->name('students.store');

Route::post('/students/timesheet', [StudentController::class, 'store'])
    ->name('timesheet.store');
//Timesheet.blade.php

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
