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

Route::get('/students', [StudentController::class, 'index'])
->name('/students');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::get('/recruiter', [RecruiterController::class, 'index'])
    ->name('recruiter.index');


    Route::get('/recruiter/create', [RecruiterController::class, 'create'])
    ->name('recruiter.create');


Route::post('/recruiter', [RecruiterController::class, 'store'])
    ->name('recruiter.store');
});

Route::get('/recruiter/students', [RecruiterController::class, 'myStudents'])->middleware('auth')
    ->name('recruiter.students');

Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->name('admin.dashboard');

Route::get('/student/dashboard', [StudentController::class, 'index'])
    ->name('student.dashboard');

require __DIR__.'/auth.php';
