<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use  Symfony\Component\HttpFoundation\File\File;
// use Symfony\Component\Mime\Encoder\Base64Encoder;
// use Intervention\Image\ImageManagerStatic as Image;

// require 'vendor/autoload.php';


trait MediaUpload
{
    public function storeMediaAsBased64($file, $dirName)
    {
        // $base64_image = "data:image/jpeg;base64, blahblahablah";

        if (preg_match('/^data:image\/(\w+);base64,/', $file)) {
            $data = substr($file, strpos($file, ',') + 1);

            $image_parts = explode(";base64,", $file);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1]; // mimes
            $data = base64_decode($data);
            $fileName = \uniqid() . "." . $image_type;

            $filePath = "tmp/$dirName/$fileName";
            Storage::disk('public')->put($filePath, $data);

            return $filePath;
        }
    }

    public function storeMediaAsFile(UploadedFile $file, $dirName)
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
