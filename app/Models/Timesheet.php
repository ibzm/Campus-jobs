<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;


    protected $table = 'timesheet'; 
    protected $fillable = [
        'user_id',
        'job_id',
        'shift_start',
        'shift_end',
        'approved',
        'hours_requested'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

 
 // In Timesheet model
public function student()
{
    return $this->belongsTo(User::class, 'user_id'); 
}


    // You can also define other relationships here, such as for the job
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id'); // Foreign key is 'job_id'
    }
}
