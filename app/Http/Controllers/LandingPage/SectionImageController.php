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
        $sectionImages->fill($request->only("section_id"));

        if ($request->hasFile("section_images")) {
            if ($sectionImages->image_url !== null) {
                $isDeleted = $this->removeMedia($sectionImages->image_url);
                if (!$isDeleted) {
                    return $this->error(500, "Error occur while deleting file", null);
                }
            }
            $file = $request->file("section_images");
            $imagePath = $this->storeMedia($file, "section");

            if (!$imagePath) {
                return $this->error(500, "Error occur while deleting file", null);
            }

            return $imagePath;
            $sectionImages->fill(["image_url" => \asset($imagePath)]);
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
