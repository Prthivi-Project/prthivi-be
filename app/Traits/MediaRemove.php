<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait MediaRemove
{
    public function removeMedia($filePath)
    {
        $result = Storage::delete($filePath);
        return $result;
    }
}
