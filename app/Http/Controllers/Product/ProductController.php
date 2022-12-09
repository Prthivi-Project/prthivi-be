<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use MediaUpload;
    use MediaRemove;

    private static $dirName = "product";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->query("id");
        $limit  = $request->query("limit") ?: 30;
        $name  = $request->query("name");
        $size  = $request->query("size");
        $status  = $request->query("status");
        $orderBy = $request->query("orderBy");

        if ($id) {
            $product = Product::with("store", "images")->findOrFail($id);
            return $this->success(200, "OK", $product);
        }

        $product = Product::query()->with([
            'images',
            "store",
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

        // order
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
            ProductResource::collection($product),
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
        $validated = $request->except("product_images");
        $validated["store_id"] = $request->user()->store->id;
        $product = Product::create($validated);

        if ($request->hasFile('product_images')) {
            $filePathArray = [];

            foreach ($request->file('product_images', []) as $file) {
                $filePath = $this->storeMedia($file, self::$dirName);

                $filePathArray[] = [
                    // 'product_id' => $product->id,
                    'image_url' => \asset("storage/" . $filePath),
                ];
            }

            $res = $product->images()->createMany($filePathArray);
            if (!$res) {
                return $this->error(500, "Error occur while creating new resource", null);
            }
        }



        return $this->success(201, "Product created", $product->load("images"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data =  Product::with('store', "images")->findOrFail($id);
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
        $product = Product::withCount('images')->with("images")->findOrFail($id);
        $product->fill($request->except("product_image"));

        if ($request->hasFile('product_images')) {
            if ($product->images) {
                $product->images()->delete();

                foreach ($product->images as $key => $image) {
                    $isDeleted = $this->removeMedia(
                        \str_replace(asset("storage/"), "", $image->image_url)
                    );

                    if (!$isDeleted) {
                        return $this->error(
                            500,
                            "Internal server error",
                            "Error occur while uploading new resource"
                        );
                    }
                }
                info("Sudah berhasil di hapsu");
            }
            $filePathArray = [];

            foreach ($request->file('product_images', []) as $key => $file) {
                $filePath = $this->storeMedia($file, self::$dirName);

                if (!$filePath) {
                    return $this->error(
                        500,
                        "Internal server error",
                        "Error occur while uploading new resource"
                    );
                }

                $filePathArray[] = [
                    'product_id' => $product->id,
                    'image_url' => \asset("storage/" . $filePath),
                    'created_at' => \now(),
                    "updated_at" => now()
                ];
            }
            $product->images()->insert($filePathArray);
        }

        $product = Product::with("images")->findOrFail($product->id);

        return $this->success(200, "Update successfully", $product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Product::findOrFail($id);

        $data->delete();
        if (!$data) {
            return $this->error(400, "Error occur while deleting resource",  "Error occur while deleting resource");
        }
        return $this->success(200, "Delete successfully", null);
    }
}
