<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration {

    public function up(): void
    {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('visa_status')->nullable();
            $table->integer('remaining_hours')->default(20);
        });

        Schema::create('category', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
        });


        Schema::create('permission', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->string('name');
        });


        Schema::create('role', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
        });


        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('role_id')->references('id')->on('role')->onDelete('cascade');
        });

      
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->references('id')->on('role')->onDelete('cascade');
            $table->foreignId('permission_id')->references('id')->on('permission')->onDelete('cascade');
        });


        Schema::create('hour_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recruiter_id')->constrained('users')->onDelete('cascade');
            $table->integer('requested_hours');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('requested_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('reason')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_dummy')->default(false);
            $table->timestamps();
        });

      

 
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('recruiter_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->dateTime('shift_start');
            $table->dateTime('shift_end');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->integer('hours_requested')->default(0);
            $table->foreignId('hour_request_id')->nullable()->constrained('hour_requests')->onDelete('cascade');
            $table->boolean('flagged')->default(false);
            $table->text('override_message')->nullable();
            $table->timestamps();
        });

        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('assigned_hours');
            $table->timestamps();
        });



        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('record_type'); 
            $table->unsignedBigInteger('record_id');
            $table->unsignedBigInteger('user_id');
            $table->text('changes')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('job_assignments');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('hour_requests');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role');
        Schema::dropIfExists('permission');
        Schema::dropIfExists('category');
        Schema::dropIfExists('users');
    }
};



