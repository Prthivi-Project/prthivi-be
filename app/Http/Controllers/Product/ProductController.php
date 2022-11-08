<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\ProductGetAllRequest;
use App\Models\Product;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Throwable;

class ProductController extends Controller
{
    use ResponseFormatter;
    use MediaUpload;
    use MediaRemove;

    private static $dirName = "product";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductGetAllRequest $request)
    {
        $id = $request->query("id");
        $limit  = $request->query("perPage");
        $limit  = $limit <= 50 ? $limit : 50;
        $name  = $request->query("name");
        $size  = $request->query("size");
        $status  = $request->query("status");
        $color = $request->query("color");

        $orderBy  = $request->query("orderBy") ?: "most_viewed";

        if ($id) {
            $product = Product::with(
                "store",
                "images",
                "colors:color,hexa_code"
            )->findOrFail($id);

            $product->view_count++;
            $product->save();
            return $this->success(200, "OK", $product);
        }

        $product = Product::query()->with([
            'images',
            "store",
            "colors"
        ]);

        if ($name) {
            $product->where("name", "LIKE", "%$name%");
        }
        if ($size) {
            $product->where("size", $size);
        }

        if ($status) {
            $product->where("status", $status);
        }

        if ($color) {
            $product->withWhereHas('colors', function ($query) use ($color) {
                $query->where('color', $color);
            });
        }

        $orderWith = "created_at";
        $orderType = "desc";

        switch ($orderBy) {
            case 'newest':
                $orderWith = 'created_at';
                $orderType = 'desc';
                break;

            case 'most_viewed':
                $orderWith = 'view_count';
                $orderType = 'desc';
                break;
        }

        $product = $product->orderBy($orderWith, $orderType)
            ->simplePaginate($limit);

        return $this->success(
            200,
            "Getting data successfully",
            $product
            // ProductResource::collection($product),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCreateRequest $request)
    {
        $validated = $request->safe()->except("product_images", "product_images_base64");

        $storeId = \auth('api')->user()->store->id;
        $validated['store_id'] = $storeId;
        try {
            $product = new Product($validated);

            $paths = $this->storeImageIfExistInRequest($request);

            $res = $product->save();
            \throw_if(!$res, Throwable::class, "Error while creating new resource");

            $res = $product->images()->createMany($paths);
            \throw_if(!$res, Throwable::class, "Error occur while uploading new resource");

            return $this->success(201, "Product created", $product->load("images"));
        } catch (FileException $e) {
            return $this->error(500, "Something went wrong", $e->getMessage());
        } catch (Throwable $th) {
            foreach ($paths as $path) {
                $this->removeMedia($path);
            }
            return $this->error(500, "Something went wrong", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data =  Product::with('store', "images", "colors:color,hexa_code")->find($id);
        \abort_if(!$data, 404, "Product not found");
        $data->view_count++;
        $data->saveOrFail();
        return $this->success(200, "OK", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::withCount('images')->with("images", 'store')->find($id);
        \abort_if(!$product, 404, "Product not found");

        $this->authorize('update', $product);
        $validated = $request->safe()->except("product_image", 'product_image_base64');

        $product->fill($validated);
        $imageFile = $request->file('product_images');
        $imageBase64Array = $request->product_image_base64;

        try {
            if (($imageFile || $imageBase64Array) && $product->images) {
                $isDeleted = $product->images()->delete();

                throw_if(!$isDeleted, Throwable::class, "Error occur while deleting resource");

                foreach ($product->images as $image) {
                    $isDeleted = $this->removeMedia(
                        \str_replace(asset("storage/"), "", $image->image_url)
                    );

                    \throw_if(!$isDeleted, FileException::class, "Cannot delete the file");
                }
            }

            $paths = $this->storeImageIfExistInRequest($request);

            $done = $product->images()->createMany($paths);

            \throw_if(!$done, Throwable::class, 'Cannot update the images');

            return $this->success(200, "Update successfully", $product);
        } catch (\Throwable $th) {
            if ($th instanceof FileException) {
                return $this->error(500, "Something went wrong", $th->getMessage());
            }
            foreach ($paths as $path) {
                $this->removeMedia($path);
            }

            return $this->error(500, "Something went wrong", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize($product);

        $product->delete();
        if (!$product) {
            return $this->error(400, "Error occur while deleting resource",  "Error occur while deleting resource");
        }
        return $this->success(200, "Delete successfully", null);
    }


    /**
     * Store image file or based64 file if any in request
     * @param Request $request
     * @return array $filePathArray
     * @throws FileException
     */
    public function storeImageIfExistInRequest(Request $request)
    {
        $filePathArray = [];
        $imageFile = $request->file('product_images');
        $imageBase64Array = $request->product_image_base64;

        $this->checkAndCreateDirIfNotExist(self::$dirName);

        if ($imageFile) {
            foreach ($imageFile as $file) {
                $filePath = $this->storeMediaAsFile($file, self::$dirName);

                \throw_if(!$filePath, FileException::class, "Error occur while uploading new resource");
                $filePathArray[] = [
                    'image_url' => \asset("storage/" . $filePath),
                ];
            }
        } elseif ($imageBase64Array) {
            foreach ($imageBase64Array as $file) {
                $filePath = $this->storeMediaAsBased64($file['image'], self::$dirName);

                \throw_if(!$filePath, FileException::class, "Error occur while uploading new resource");

                $filePathArray[] = [
                    'image_url' => \asset("storage/" . $filePath),
                    'color_id' => $file['color_id'] ?: null,
                    'priority_level' => $file['priority_level'] ?: 1,
                    'created_at' => \now(),
                    "updated_at" => now()
                ];
            }
        }

        return $filePathArray;
    }
}
