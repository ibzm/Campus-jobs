<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class job extends Model
{
public function recruiter()
{
    return $this->belongsTo(User::class, 'recruiter_id');
}

public function students()
{
    return $this->belongsToMany(User::class, 'job_assignments', 'job_id', 'student_id');
}


}