<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'first_name' => 'John',
                'second_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password1'),
                'visa_status' => '20 hours',
                'remaining_hours' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 2,
                'first_name' => 'Jane',
                'second_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'password' => bcrypt('password2'),
                'visa_status' => null,
                'remaining_hours' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 3,
                'first_name' => 'Alice',
                'second_name' => 'Brown',
                'email' => 'ibzm99@outlook.com',
                'password' => bcrypt('password3'),
                'visa_status' => '20 hours',
                'remaining_hours' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 4,
                'first_name' => 'Bob',
                'second_name' => 'White',
                'email' => 'ibzmgaming@gmail.com',
                'password' => bcrypt('password4'),
                'visa_status' => null,
                'remaining_hours' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 5,
                'first_name' => 'Charlie',
                'second_name' => 'Black',
                'email' => 'charlie.black@example.com',
                'password' => bcrypt('password5'),
                'visa_status' => '20 hours',
                'remaining_hours' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        DB::table('category')->insert([
            ['id' => 1, 'name' => 'Student'],
            ['id' => 2, 'name' => 'Logistics'],
            ['id' => 3, 'name' => 'Recruiter'],
        ]);

        DB::table('permission')->insert([
            ['id' => 1, 'category_id' => 1, 'name' => 'Create Timesheet'],
            ['id' => 2, 'category_id' => 1, 'name' => 'View Timesheet'],
            ['id' => 3, 'category_id' => 2, 'name' => 'Approve Timesheet'],
            ['id' => 4, 'category_id' => 3, 'name' => 'Selling Flipflops'],
            ['id' => 5, 'category_id' => 3, 'name' => 'Testing All'],
        ]);

        DB::table('role')->insert([
            ['id' => 1, 'name' => 'Student'],
            ['id' => 2, 'name' => 'Admin'],
            ['id' => 3, 'name' => 'Recruiter'],
        ]);

      
        DB::table('user_role')->insert([
            ['user_id' => 1, 'role_id' => 3], // recruiter
            ['user_id' => 2, 'role_id' => 1], // student
            ['user_id' => 3, 'role_id' => 3], // recruiter
            ['user_id' => 4, 'role_id' => 1], // student
            ['user_id' => 5, 'role_id' => 2], // admin
        ]);

        DB::table('role_permission')->insert([
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 1, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
            ['role_id' => 3, 'permission_id' => 4],
            ['role_id' => 3, 'permission_id' => 5],
        ]);

        // DB::table('hour_requests')->insert([
        //     [
        //         'student_id' => 2,
        //         'recruiter_id' => 3,
        //         'requested_hours' => 10,
        //         'status' => 'approved',
        //         'requested_date' => '2025-04-14',
        //         'start_time' => '08:00:00',
        //         'end_time' => '12:00:00',
        //         'reason' => 'Approved as per visa hours',
        //         'comment' => 'This hour request is approved.',
        //         'is_dummy' => false,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'student_id' => 4,
        //         'recruiter_id' => 3,
        //         'requested_hours' => 12,
        //         'status' => 'pending',
        //         'requested_date' => '2025-04-15',
        //         'start_time' => '09:00:00',
        //         'end_time' => '14:00:00',
        //         'reason' => null,
        //         'comment' => 'Dummy request used as fallback.',
        //         'is_dummy' => true,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        // ]);

        DB::table('jobs')->insert([
            ['recruiter_id' => 3, 'title' => 'Research Assistant', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['recruiter_id' => 3, 'title' => 'Teaching Assistant', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['recruiter_id' => 5, 'title' => 'Teaching Assistant', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::table('job_assignments')->insert([
            ['job_id' => 1, 'student_id' => 2, 'assigned_hours' => 10, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['job_id' => 2, 'student_id' => 4, 'assigned_hours' => 12, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['job_id' => 2, 'student_id' => 3, 'assigned_hours' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // DB::table('timesheets')->insert([
        //     [
        //         'user_id' => 2,
        //         'job_id' => 1,
        //         'shift_start' => '2025-04-14 08:00:00',
        //         'shift_end' => '2025-04-14 12:00:00',
        //         'status' => 'approved',
        //         'hours_requested' => 10,
        //         'hour_request_id' => 1,
        //         'flagged' => false,
        //         'override_message' => null,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'user_id' => 4,
        //         'job_id' => 2,
        //         'shift_start' => '2025-04-15 09:00:00',
        //         'shift_end' => '2025-04-15 14:00:00',
        //         'status' => 'pending',
        //         'hours_requested' => 12,
        //         'hour_request_id' => 2,
        //         'flagged' => false,
        //         'override_message' => null,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        // ]);
    }
}
