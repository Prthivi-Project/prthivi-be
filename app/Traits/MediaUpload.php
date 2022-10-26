<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait MediaUpload
{
    public function storeMedia(UploadedFile $file, $dirName)
    {
        $name = \uniqid() . '-' . $file->getClientOriginalName();
        $filePath = Storage::putFileAs("public/tmp/$dirName", $file, $name);

        return $filePath;
    }

    public function checkAndCreateDirIfNotExist($dirName)
    {
        if (!Storage::exists("public/tmp/$dirName")) {
            Storage::makeDirectory("tmp/$dirName");
        }

        if (!Storage::exists("public/preview/$dirName")) {
            Storage::makeDirectory("preview/$dirName");
        }
        if (!Storage::exists("public/thumb/$dirName")) {
            Storage::makeDirectory("public/thumb/$dirName");
        }
    }
}
