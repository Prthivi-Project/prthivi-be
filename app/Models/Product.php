<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "price",
        "available",
        "size",
        "store_id",
        "fabric_composition",
    ];

    // relational
    public function categories()
    {
        return $this->belongsToMany(Category::class, "user_roles", "product_id");
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
