<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create users table first
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('visa_status')->nullable(); // Only needed for students
            $table->integer('remaining_hours')->default(20); // Default to 20 hours
        });

        // Insert sample users into 'users' table
        DB::table('users')->insert([
            ['id' => 1, 'first_name' => 'John', 'second_name' => 'Doe', 'email' => 'john.doe@example.com', 'password' => bcrypt('password1'), 'visa_status' => '20 hours', 'remaining_hours' => 20],
            ['id' => 2, 'first_name' => 'Jane', 'second_name' => 'Smith', 'email' => 'jane.smith@example.com', 'password' => bcrypt('password2'), 'visa_status' => null, 'remaining_hours' => 20],  // Adjusted to match columns
            ['id' => 3, 'first_name' => 'Alice', 'second_name' => 'Brown', 'email' => 'alice.brown@example.com', 'password' => bcrypt('password3'), 'visa_status' => '20 hours', 'remaining_hours' => 20],
            ['id' => 4, 'first_name' => 'Bob', 'second_name' => 'White', 'email' => 'bob.white@example.com', 'password' => bcrypt('password4'), 'visa_status' => null, 'remaining_hours' => 20], // Adjusted to match columns
            ['id' => 5, 'first_name' => 'Charlie', 'second_name' => 'Black', 'email' => 'charlie.black@example.com', 'password' => bcrypt('password5'), 'visa_status' => '20 hours', 'remaining_hours' => 20],
        ]);
        
        
        // Create category table for roles like Student, Recruiter, Logistics
        Schema::create('category', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
        });

        DB::table('category')->insert([
            ['id' => 1, 'name' => 'Student'],
            ['id' => 2, 'name' => 'Logistics'],
            ['id' => 3, 'name' => 'Recruiter'],
        ]);

        // Create permission table for what roles can do
        Schema::create('permission', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->string('name');
        });

        DB::table('permission')->insert([
            ['id' => 1, 'category_id' => 1, 'name' => 'Create Timesheet'],
            ['id' => 2, 'category_id' => 1, 'name' => 'View Timesheet'],
            ['id' => 3, 'category_id' => 2, 'name' => 'Approve Timesheet'],
            ['id' => 4, 'category_id' => 3, 'name' => 'Selling Flipflops'],
            ['id' => 5, 'category_id' => 3, 'name' => 'Testing All'],
        ]);

        // Create role table for Student, Admin, and Recruiter
        Schema::create('role', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
        });

        DB::table('role')->insert([
            ['id' => 1, 'name' => 'Student'],
            ['id' => 2, 'name' => 'Admin'],
            ['id' => 3, 'name' => 'Recruiter'],
        ]);

      
        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('role_id')->references('id')->on('role')->onDelete('cascade');
        });

        DB::table('user_role')->insert([
            ['user_id' => 1, 'role_id' => 3],# recuiter
            ['user_id' => 2, 'role_id' => 1],# Student
            ['user_id' => 3, 'role_id' => 3],
            ['user_id' => 4, 'role_id' => 1],
            ['user_id' => 5, 'role_id' => 2],
        ]);


        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->references('id')->on('role')->onDelete('cascade');
            $table->foreignId('permission_id')->references('id')->on('permission')->onDelete('cascade');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('recruiter_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        DB::table('jobs')->insert([
            ['recruiter_id' => 3, 'title' => 'Research Assistant'],
            ['recruiter_id' => 3, 'title' => 'Teaching Assistant'],
            ['recruiter_id' => 5, 'title' => 'Teaching Assistant'],
        ]);

        // Create timesheet table for students
        Schema::create('timesheet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->dateTime('shift_start');
            $table->dateTime('shift_end');
            $table->boolean('approved')->default(false);
            $table->integer('hours_requested')->default(0);
            $table->timestamps();
        });

        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('assigned_hours');
            $table->timestamps();
        });

        DB::table('job_assignments')->insert([
            ['student_id' => 2, 'job_id' => 1, 'assigned_hours' => 10],  
            ['student_id' => 4, 'job_id' => 2, 'assigned_hours' => 12],  
            ['student_id' => 3, 'job_id' => 2, 'assigned_hours' => 8],  
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category');
        Schema::dropIfExists('permission');
        Schema::dropIfExists('role');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('users');
        Schema::dropIfExists('students');
        Schema::dropIfExists('timesheet');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_assignments');
    }
};

  

