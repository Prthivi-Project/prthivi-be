<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
        // return $this->belongsToMany(User::class, 'user_roles', 'user_id', "role_id");
    }
}
