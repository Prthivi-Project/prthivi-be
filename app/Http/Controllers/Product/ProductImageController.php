<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImageUpdateRequest;
use App\Models\ProductImages;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    use MediaUpload;
    use MediaRemove;
    use ResponseFormatter;

    private static $dirName = "product";

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductImageUpdateRequest $request, $id)
    {
        $image = ProductImages::findOrFail($id);
        $image->fill($request->except("product_image"));
        if ($image->image_url) {
            $this->removeMedia(\str_replace(\asset("storage"), "", $image->image_url));
        }

        $fileImage = $request->file("product_image");
        $filePath = $this->storeMedia($fileImage, self::$dirName);
        if (!$filePath) {
            return $this->error(
                500,
                "Error while upload file",
                "Error while upload file"
            );
        }

        $image->image_url = \asset("storage/$filePath");

        $image = $image->saveOrFail();


        return $this->success(200, "Update successfully", $image);
    }
}
