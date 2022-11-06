<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImageUpdateRequest;
use App\Models\Product;
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

    public function store(ProductImageUpdateRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $this->authorize('create', [ProductImages::class, $product]);

        $image = new ProductImages($request->safe()->except("product_image", 'product_image_base64'));

        $imageBase64 = $request->product_image_base64;
        $imageFile = $request->file('product_image');
        $filePath = null;
        if ($imageBase64) {
            $filePath = $this->storeMediaAsBased64($imageBase64, self::$dirName);
            if (!$filePath) {
                return $this->error(
                    500,
                    "Error while upload file",
                    "Error while upload file"
                );
            }
        } elseif ($imageFile) {
            $filePath = $this->storeMediaAsFile($imageBase64, self::$dirName);
            if (!$filePath) {
                return $this->error(
                    500,
                    "Error while upload file",
                    "Error while upload file"
                );
            }
        }

        if (!$filePath) {
            return $this->error(
                500,
                "Error while upload file",
                "Error while upload file"
            );
        }

        $image->image_url = \asset("storage/$filePath");

        $image = $image->saveOrFail();


        return $this->success(200, "Product images has been uploaded successfuly", $image);
    }


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
        $this->authorize($image);

        $image->fill($request->safe()->except("product_image", 'product_image_base64'));
        $imageBase64 = $request->product_image_base64;
        $imageFile = $request->file('product_image');
        $filePath = $image->image_url;
        if ($filePath) {
            $this->removeMedia(\str_replace(\asset("storage"), "", $image->image_url));
        }
        if ($imageBase64) {
            $filePath = $this->storeMediaAsBased64($imageBase64, self::$dirName);
            if (!$filePath) {
                return $this->error(
                    500,
                    "Error while upload file",
                    "Error while upload file"
                );
            }
        } elseif ($imageFile) {
            $filePath = $this->storeMediaAsFile($imageBase64, self::$dirName);
            if (!$filePath) {
                return $this->error(
                    500,
                    "Error while upload file",
                    "Error while upload file"
                );
            }
        }

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
