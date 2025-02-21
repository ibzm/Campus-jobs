<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    public function permissions()
{
    return $this->hasMany(Permission::class);
}

}
