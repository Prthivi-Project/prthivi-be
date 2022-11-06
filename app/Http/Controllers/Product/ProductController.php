<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $id = $request->query("id");
        $limit  = $request->query("limit") ?: 30;
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
        $product = Product::create($validated);

        $filePathArray = [];

        if ($request->hasFile("product_images")) {
            foreach ($request->file('product_images', []) as $file) {
                $filePath = $this->storeMediaAsFile($file, self::$dirName);

                $filePathArray[] = [
                    'image_url' => \asset("storage/" . $filePath),
                ];
            }
        } elseif ($request->product_image_base64) {
            foreach ($request->product_image_base64 as $key => $file) {
                $filePath = $this->storeMediaAsBased64($file['image'], self::$dirName);
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
                    'color_id' => $file['color_id'] ?: null,
                    'priority_level' => $file['priority_level'] ?: 1,
                    'created_at' => \now(),
                    "updated_at" => now()
                ];
            }
        }

        $res = $product->images()->insert($filePathArray);
        if (!$res) {
            return $this->error(500, "Error occur while creating new resource", null);
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
        $data =  Product::with('store', "images", "colors:color,hexa_code")->findOrFail($id);
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
        $product = Product::withCount('images')->with("images", 'store')->findOrFail($id);
        $this->authorize('update', $product);

        $product->fill($request->safe()->except("product_image"));
        $imageFile = $request->file('product_images');
        $imageBase64Array = $request->product_image_base64;

        if ($imageFile || $imageBase64Array) {
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
            }


            $filePathArray = [];

            if ($imageFile) {
                foreach ($imageFile as $key => $file) {
                    $filePath = $this->storeMediaAsFile($file, self::$dirName);

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
            } elseif ($imageBase64Array) {
                foreach ($imageBase64Array as $key => $value) {
                    $filePath = $this->storeMediaAsBased64($value['image'], self::$dirName);

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
                        'color_id' => $value['color_id'] ?: null,
                        'priority_level' => $value['priority_level'] ?? 1,
                        'created_at' => \now(),
                        "updated_at" => now()
                    ];
                }
            }

            $product->images()->insert($filePathArray);
        }


        return $this->success(200, "Update successfully", $product->load('images'));
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
}
