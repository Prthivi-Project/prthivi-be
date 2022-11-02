<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSectionImagesRequest;
use App\Models\LandingPage\SectionImages;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

class SectionImageController extends Controller
{
    use MediaUpload;
    use MediaRemove;
    use ResponseFormatter;

    private static $dirName = 'section';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSectionImagesRequest $request, SectionImages $sectionImages)
    {
        $base64image = $request->section_images_64base;
        $hasImageFile = $request->hasFile("section_images");
        $imagePath = '';

        if ($base64image || $hasImageFile) {
            if ($sectionImages->image_url !== null) {
                $isDeleted = $this->removeMedia($sectionImages->image_url);
                if (!$isDeleted) {
                    return $this->error(500, "Error occur while deleting file", null);
                }
            }

            if ($request->hasFile("section_images")) {
                $file = $request->file("section_images");
                $imagePath = $this->storeMedia($file, self::$dirName);
                if (!$imagePath) {
                    return $this->error(500, "Error occur while deleting file", null);
                }
            } else if ($base64image) {
                $imagePath = $this->storeMediaAsBased64($base64image, self::$dirName);
            }


            $sectionImages->image_url = \asset('storage/' .  $imagePath);
        }
        $sectionImages->saveOrFail();

        return $this->success(200, "UPDATED", $sectionImages->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
