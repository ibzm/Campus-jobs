<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{

    protected $table = 'timesheet';
    protected $fillable = [
        'user_id',
        'recruiter_name',
        'requested_hours',
        'date_time',
        'remaining_hours',
    ];
}
