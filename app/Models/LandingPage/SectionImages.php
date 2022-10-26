<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionImages extends Model
{
    use HasFactory;

    protected $fillable = [
        "image_url",
        "section_id",
    ];

    //relation
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
