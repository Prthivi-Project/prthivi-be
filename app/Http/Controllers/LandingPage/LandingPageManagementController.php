<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\LandingPageManagementRequest;
use App\Http\Requests\UpdateLandingPageRequest;
use App\Models\LandingPage\Images;
use App\Models\LandingPage\Section;
use App\Models\LandingPage\SectionImages;
use App\Models\ProductImages;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingPageManagementController extends Controller
{
    use MediaUpload;
    use MediaRemove;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private static $dirName = "section";

    public function index()
    {
        $data = Section::orderBy("number")
            ->with("images")
            ->get();

        if (!$data) {
            return $this->error(400, "Bad Request", null);
        }
        return $this->success(200, "OK", $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LandingPageManagementRequest $request)
    {
        $validated = $request->except("section_images");
        $imageFiles = $request->file("section_images", []);


        $section = Section::create($validated);

        $this->checkAndCreateDirIfNotExist(self::$dirName);

        if ($request->section_images_64base) {
            $imageArr = array();
            foreach ($request->section_images_64base as $key => $file) {
                $filePath =  $this->storeMediaAsBased64($file, self::$dirName);
                $imageArr[] = ['image_url' => asset("storage/" . $filePath)];
            }

            $section->images()->createMany($imageArr);
        }

        if ($request->hasFile("section_images")) {
            $imgArray = array();
            foreach ($imageFiles as $file) {
                $imagePath = $this->storeMediaAsFile($file, self::$dirName);
                if (!$imagePath) {
                    return $this->error(
                        500,
                        "Error occur while uploading photo",
                        "Error occur while uploading photo"
                    );
                }

                $imgArray[] = ["image_url" => \asset("storage/" . $imagePath)];
            }

            $section->images()->createMany($imgArray);


            if (!$section) {
                return $this->error(
                    500,
                    "Error occur while creating new resource",
                    "Error occur while uploading photo"
                );
            }
        }

        return $this->success(201, "CREATED", $section->load("images"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $section = Section::with('images')->findOrFail($id);
        return $this->success(200, "OK", $section);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLandingPageRequest $request, $id)
    {
        $section = Section::with("images")->find($id);
        if (!$section) {
            return $this->error(404, "Not found", "No query result for section id $id");
        }
        $validated = $request->except("section_images", "section_images_64base");

        $section->fill($validated);

        if ($request->hasFile("section_images") || $request->section_images_64base) {
            if (\count($section->images) > 0) {
                foreach ($section->images as $file) {
                    $filePath  = str_replace(\asset("storage"), "", $file->image_url);
                    $this->removeMedia($filePath);
                }

                $section->images()->delete();
            }

            $imgArray = array();
            foreach ($request->file("section_images", []) as $file) {
                $imagePath = $this->storeMediaAsFile($file, self::$dirName);
                if (!$imagePath) {
                    return $this->error(500, "Error occur while uploading photo", null);
                }

                $imgArray[] = [
                    "section_id" => $section->id,
                    "image_url" => \asset("storage/" . $imagePath),
                    "created_at" => \now(),
                    "updated_at" => now()
                ];
            }
            foreach ($request->section_images_64base as $file) {
                $imagePath =  $this->storeMediaAsBased64($file, self::$dirName);

                if (!$imagePath) {
                    return $this->error(500, "Error occur while uploading photo", null);
                }

                $imgArray[] = [
                    "section_id" => $section->id,
                    "image_url" => \asset("storage/" . $imagePath),
                    "created_at" => \now(),
                    "updated_at" => now()
                ];
            }
            $section->images()->insert($imgArray);
        }

        $section->saveOrFail();

        return $this->success(200, "UPDATED", $section->load("images"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Section::class);
        $section = Section::with("images")->findOrFail($id);
        if (!$section) {
            return $this->error(404, "Not found", "No query result for section id $id");
        }

        // delete file image
        if (\count($section->images) > 0) {
            foreach ($section->images as $file) {
                $filePath  = str_replace(\asset("storage/"), "", $file->image_url);
                $isRemoved = $this->removeMedia($filePath);
                if (!$isRemoved) {
                    return $this->error(400, "Cannot remove file", null);
                }
            }
        }
        $section = $section->delete();

        if (!$section) {
            return $this->error(500, "Internal Server error", "Error occur while deleting resource");
        }
        return $this->success(200, "Deleted", null);
    }
}
