<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreCreateRequest;
use App\Http\Requests\Store\StoreUpdateRequest;
use App\Models\Store;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use ResponseFormatter;
    use MediaUpload;
    use MediaRemove;

    private static $dirName = "store";

    public function index(Request $request)
    {
        $id = $request->id;
        $name = $request->name;

        if ($id) {
            $store = Store::with("products")->findOrFail($id);
            return $this->success(200, "OK", $store);
        }
        $store = Store::query()->with("products");

        if ($name) {
            $store->where("name", "LIKE", "%" . $name . "%");
        }

        $store = $store->simplePaginate();
        return $this->success(200, "OK", $store);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(StoreCreateRequest $request)
    {
        $validated = $request->except("store_image");
        $store = Store::create($validated);
        if (!$store) {
            return $this->error(400, "Error occur while creating new resource", null);
        }

        if ($request->hasFile("store_image")) {
            $file = $request->file("store_image");
            $path = $this->storeMedia($file, self::$dirName);
            $store->fill([
                "photo_url" => \asset("storage/$path")
            ])->saveOrFail();
        }

        return $this->success(201, "Product created", $store);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateRequest $request, $id)
    {
        $store = Store::findOrFail($id);

        $store->fill($request->except("store_image"));
        if ($request->hasFile("store_image")) {
            $file = $request->file("store_image");
            $path = $this->storeMedia($file, self::$dirName);
            $store->fill([
                "photo_url" => \asset("storage/$path")
            ])->saveOrFail();
        }

        $store->saveOrFail();

        return $this->success(200, "Update successfully", $store);
    }
}
