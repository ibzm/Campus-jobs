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
        Schema::create( 'students', function (Blueprint $table) {
            $table->id();   
            $table->string('first_name');
            $table->string('email')->unique();
            $table->string('visa_status');
            $table->integer('remaining_hours');
            $table->integer('student_id');
        });

        DB::table('students')->insert([
            ['id' => 1, 'first_name' => 'ibzm', 'email' => 'ibzm99@outlook.com','visa_status' => '20 hours', 'remaining_hours' => 20,  'student_id' => 2212112]    
        ]);


              //role based enforcement
              Schema::create('category', function (Blueprint $table) {
                $table->id()->primary();
                $table->string('name');
            });
    
            DB::table('category')->insert([
                ['id' => '1', 'name' => 'Student'],
                ['id' => '2', 'name' => 'Logisitics'],
                ['id' => '3', 'name' => 'Recruiter'],
            ]);

          //Permissions linked to role so what they can do e.g Create a timesheet
          Schema::create('permission', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignID('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->string('name');
        });
   
        DB::table('permission')->insert([
            ['id' => '1', 'category_id' => '1', 'name' => 'Create Timesheet'],
            ['id' => '2', 'category_id' => '1', 'name' => 'View Timesheet'],
            ['id' => '3', 'category_id' => '2', 'name' => 'Approve Timesheet'],
            ['id' => '4', 'category_id' => '3', 'name' => 'selling flipflops'],
            ['id' => '5', 'category_id' => '3', 'name' => 'testing all'],
        ]);

        //role are the chose role names so student admin and recruiter
        Schema::create('role', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
        });
        DB::table('role')->insert([
            ['id' => '1', 'name' => 'Student'],
            ['id' => '2', 'name' => 'Admin'],
            ['id' => '3', 'name' => 'Recruiter']
        ]);
   

        //User role attatached to each user so 1 will be a student e.g 
        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('role_id')->references('id')->on('role')->onDelete('cascade');
        });
        DB::table('user_role')->insert([
            ['user_id' => '1', 'role_id' => '1' ]
        ]);


        
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignID('role_id')->references('id')->on('role')->onDelete('cascade');
            $table->foreignID('permission_id')->references('id')->on('permission')->onDelete('cascade');
        });

        //role permissions is what gives the user the role permission so this can go as high as u have diffrent things a user can do 
        DB::table('role_permission')->insert([
            ['role_id' => '1', 'permission_id' => '1'],
            ['role_id' => '1', 'permission_id' => '2'],
            ['role_id' => '1', 'permission_id' => '3'],
        ]);


    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permission');
        Schema::dropIfExists('role');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('category');
    }
};
