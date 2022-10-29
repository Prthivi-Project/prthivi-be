<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait MediaUpload
{
    public function storeMedia(UploadedFile $file, $dirName)
    {
        $name = \uniqid() . '-' . $file->getClientOriginalName();
        $filePath = Storage::putFileAs("tmp/$dirName", $file, $name);

        return $filePath;
    }

    public function checkAndCreateDirIfNotExist($dirName)
    {
        if (!Storage::exists("tmp/$dirName")) {
            Storage::makeDirectory("tmp/$dirName");
        }

        if (!Storage::exists("preview/$dirName")) {
            Storage::makeDirectory("preview/$dirName");
        }
        if (!Storage::exists("thumb/$dirName")) {
            Storage::makeDirectory("thumb/$dirName");
        }
    }
}
