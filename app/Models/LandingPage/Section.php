<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        "number",
        "section_title",
        "section_description"
    ];

    // relational
    public function images()
    {
        return $this->hasMany(SectionImages::class);
    }

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
