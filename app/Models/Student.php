<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students'; 
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }    protected $fillable = [
        'first_name', 
        'email', 
        'visa_status', 
        'remaining_hours', 
        'student_id'
    ];
}
