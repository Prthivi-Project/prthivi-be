<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        "section_id"
    ];

    // relation
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
