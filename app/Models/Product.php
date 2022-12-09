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
        "status",
        "size",
        "view_count",
        "store_id",
        "fabric_composition",
    ];

    // relational
    public function categories()
    {
        return $this->belongsToMany(Category::class, "product_categories", "product_id");
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function owner()
    {
        return $this->hasOneThrough(User::class, Store::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImages::class);
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }



    // helpers

    public function isOwnerProduct(User $user)
    {
        return $this->store_id === $user->store->id;
    }
}
