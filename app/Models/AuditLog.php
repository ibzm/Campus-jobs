<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['record_type', 'record_id', 'user_id', 'changes'];
}
