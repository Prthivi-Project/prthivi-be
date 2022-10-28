<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        "address",
        "photo_url",
        "map_location",
        'user_id',
    ];

    // relational
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
