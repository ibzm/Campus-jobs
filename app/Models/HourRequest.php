<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class HourRequest extends Model
{
    use HasFactory;


    protected $fillable = [
        'student_id',      
        'recruiter_id',
        'requested_hours',
        'status',   
        'requested_date',
        'start_time',        
        'end_time',          
        'recurrence_weeks',  
        'comment',  
  
    ];
}