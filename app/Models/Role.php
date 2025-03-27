<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'role';
    public function roles()
{
    return $this->belongsToMany(Role::class, 'user_role');
}

}
