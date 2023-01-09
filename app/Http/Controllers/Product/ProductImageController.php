<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImageCreateRequest;
use App\Http\Requests\ProductImageUpdateRequest;
use App\Models\Product;
use App\Models\ProductImages;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class ProductImageController extends Controller
{
    use MediaUpload;
    use MediaRemove;

    private static $dirName = "product";

    public function store(ProductImageCreateRequest $request)
    {
        $product = Product::find($request->product_id);
        \abort_if(!$product, 404, "Product not found");

        $this->authorize('create', [ProductImages::class, $product]);

        $validated = $request->safe()->except("product_image", 'product_image_base64');

        $image = new ProductImages($validated);

        $imageBase64 = $request->product_image_base64;
        $imageFile = $request->file('product_image');
        $filePath = null;
        try {
            $this->checkAndCreateDirIfNotExist(self::$dirName);

            if ($imageBase64) {
                try {
                    $filePath = $this->storeMediaAsBased64($imageBase64, self::$dirName);
                    \throw_if(!$filePath, FileException::class, "Error while uploading file");
                } catch (BadRequestHttpException $th) {
                    return $this->error(400, "Bad Request", $th->getMessage());
                }
            } elseif ($imageFile) {
                $filePath = $this->storeMediaAsFile($imageBase64, self::$dirName);

                \throw_if(!$filePath, FileException::class, "Error while uploading file");
            }

            $image->image_url = \asset("storage/$filePath");

            $image->saveOrFail();

            return $this->success(200, "Product images has been uploaded successfuly", $image);
        } catch (FileException $th) {
            return $this->error(500, "Something went wrong", $th->getMessage());
        } catch (Throwable $th) {
            \unlink($filePath);
            return $this->error(500, "Something went wrong", $th->getMessage());
        }
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
        $image = ProductImages::find($id);
        \abort_if(!$image, 404, "Product images not found");

        $this->authorize($image);

        $image->fill($request->safe()->except("product_image", 'product_image_base64'));
        $imageBase64 = $request->product_image_base64;
        $imageFile = $request->file('product_image');
        $filePath = $image->image_url;
        try {
            if ($filePath) {
                $isDeleted = $this->removeMedia(\str_replace(\asset("storage"), "", $image->image_url));

                \throw_if(!$isDeleted, FileException::class, "Something went wrong");
            }
            if ($imageBase64) {
                $filePath = $this->storeMediaAsBased64($imageBase64, self::$dirName);

                \throw_if(!$filePath, FileException::class, "Error while uploading file");
            } elseif ($imageFile) {
                $filePath = $this->storeMediaAsFile($imageBase64, self::$dirName);

                \throw_if(!$filePath, FileException::class, "Error while uploading file");
            }

            $image->image_url = \asset("storage/$filePath");

            $image->saveOrFail();

            return $this->success(200, "Update successfully", $image);
        } catch (\Throwable $th) {
            if (!($th instanceof FileException)) {
                try {
                    \unlink($filePath);
                    return $this->error(500, "Something went wrong", $th->getMessage());
                } catch (\Throwable $th) {
                }
            }

            return $this->error(500, "Something went wrong", $th->getMessage());
        }
    }
}
