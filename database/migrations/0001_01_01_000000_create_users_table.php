<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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
        });
        DB::table('users')->insert([
            ['id' => 1, 'first_name' => 'ibzm', 'second_name' => 'zurita', 'email' => 'ibzm99@outlook.com','password' => '$2y$10$IynevDBP1N6Y0q7ENBn9NuPdz7WRFiqHyOFbMHsNFuM9IYCYSZnMO']    
        ]);

        
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        
        Schema::create('Timesheet', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignID('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreignID('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->string('recruiter_name');
            $table->string('requested_hours');
            $table->timestamp('date_time');
            $table->integer('remaining_hours');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
