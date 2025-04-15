<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;


    protected $table = 'timesheets'; 
    protected $fillable = [
        'user_id',
        'job_id',
        'shift_start',
        'shift_end',
        'approved',
        'hours_requested',
        'flagged',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
public function student()
{
    return $this->belongsTo(User::class, 'user_id'); 
}



    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id'); 
    }
}
