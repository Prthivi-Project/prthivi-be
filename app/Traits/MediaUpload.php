<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

// use Symfony\Component\Mime\Encoder\Base64Encoder;

// require 'vendor/autoload.php';


trait MediaUpload
{
    /**
     * Funciton ini digunakan untuk memindahkan file ke dalam storage
     * @param mixed $file
     * @param string $dirname
     * @return string|false
     * @throws Illuminate\Validation\ValidationException
     * @throws  Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException
     */
    public function storeMediaAsBased64($file, $dirName)
    {
        $isValid = preg_match('/^data:image\/(\w+);base64,/', $file);
        \throw_if(!$isValid, BadRequestHttpException::class, "Bad base64 image");

        $data = substr($file, strpos($file, ',') + 1);

        $imageParts = explode(";base64,", $file);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1]; // mimes

        $data = base64_decode($data);

        \throw_if(!$data, CannotWriteFileException::class, "Cannot decode base64");

        $fileName = \uniqid() . "." . $imageType;

        $filePath = "tmp/$dirName/$fileName";
        $isStored = Storage::disk('public')->put($filePath, $data);
        \throw_if(!$isStored, CannotWriteFileException::class, "Cannot write the file");

        return $filePath;
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
