<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class HourRequest extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }
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