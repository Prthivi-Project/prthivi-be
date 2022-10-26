<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    protected $fillable = [
        "image_url",
    ];

    //relation
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
