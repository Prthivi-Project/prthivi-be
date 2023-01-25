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

    // helper
    public static  $admin = 2;

    public static  $vendor = 3;

    public static  $customer  = 4;


    public function users()
    {
        return $this->hasMany(User::class);
    }
}
