<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory, Notifiable; 

    protected $table = 'users'; 
    /**
    
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function students()
    {
        return $this->hasMany(User::class, 'recruiter_id')->where('role', 'student');
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id')->where('role', 'recruiter');
    }
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
    
public function jobs()
{
    return $this->hasMany(Job::class, 'recruiter_id');
}

public function assignedJobs()
{
    return $this->belongsToMany(Job::class, 'job_assignments', 'student_id', 'job_id')
        ->withPivot('assigned_hours')
        ->withTimestamps();
}

public function roles()
{
    return $this->belongsToMany(Role::class, 'user_role');
}

public function hasRole($roleName)
{
    return $this->roles->contains('name', $roleName);
}
}
