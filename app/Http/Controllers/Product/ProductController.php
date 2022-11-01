<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseFormatter;
    use MediaUpload;

    private static $dirName = "product";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->query("id");
        $limit  = $request->query("limit") ?? 50;
        $name  = $request->query("name");
        $available  = $request->query("available");

        if ($id) {
            $product = Product::find($id);
            if (!$product) {
                return $this->error(404, "Product not found.", null);
            }
            return $this->success(200, "OK", $product);
        }

        $product = Product::query()->with('images', 'store');
        if ($name) {
            $product->where("name", "LIKE", "%$name%");
        }
        if ($available) {
            $product->where("available", $available);
        }

        $product = $product->limit($limit)->get();
        return $this->success(
            200,
            "Getting data successfully",
            $product,
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->except("product_images");
        $product = Product::create($validated);

        if ($request->hasFile('product_images')) {
            $filePathArray = [];

            foreach ($request->file('product_images', []) as $file) {
                $filePath = $this->storeMedia($file, self::$dirName);

                $filePathArray[] = [
                    'product_id' => $product->id,
                    'image_url' => \asset($filePath),
                ];
            }

            $product->images()->insert($filePathArray);
        }

        if (!$product) {
            return $this->error(500, "Error occur while creating new resource", null);
        }

        return $this->success(201, "Product created", $product);
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
    public function update(Request $request, $id)
    {
        //
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
