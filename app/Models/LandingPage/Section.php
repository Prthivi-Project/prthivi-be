<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        "images_id",
        "number"
    ];

    // relational
    public function images()
    {
        return $this->hasMany(Images::class);
    }

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
