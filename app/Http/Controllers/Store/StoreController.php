<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreCreateRequest;
use App\Http\Requests\Store\StoreUpdateRequest;
use App\Http\Requests\StoreGetAllRequest;
use App\Models\Store;
use App\Traits\MediaRemove;
use App\Traits\MediaUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Throwable;

class StoreController extends Controller
{
    use MediaUpload;
    use MediaRemove;

    private static $dirName = "store";

    public function index(StoreGetAllRequest $request)
    {
        $id = $request->id;
        $name = $request->name;

        if ($id) {
            $store = Store::with("products")->findOrFail($id);
            return $this->success(200, "OK", $store);
        }
        $store = Store::query()->withCount("products")->with(['products' => function ($query) {
            $query->with('images')->orderBy('view_count', 'desc')->limit(10);
        }]);

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
        $this->authorize('create', Store::class);

        $validated = $request->safe()->except("store_image", "store_image_base64");

        $validated['user_id'] = $request->user()->id;

        $validated['slug'] = Str::slug($request->name . " " . Str::random(3));

        $store = new Store($validated);

        $path = "";
        try {
            $path = $this->storeImageIfExistInRequest($request);

            $store->fill([
                "photo_url" => \asset("storage/" . $path),
            ]);

            $store->saveOrFail();

            $roleUpdated = $store->user()->update(['role_id' => 3]);

            \throw_if(!$roleUpdated, Throwable::class, "Error occur while update role");

            return $this->success(200, "Create store successfully", $store);
        } catch (FileException $e) {
            return $this->error(500, "Something went wrong", $e->getMessage());
        } catch (\Throwable $th) {
            $isDeleted = $this->removeMedia($path);
            \throw_if(!$isDeleted, Throwable::class, "Cannot delete the file");
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
    public function update(StoreUpdateRequest $request, $slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();

        $this->authorize('update', $store);

        $validated = $request->safe()->except("store_image", "store_image_base64");

        $store->fill($validated);

        try {
            $this->checkAndCreateDirIfNotExist(self::$dirName);
            $path = $this->storeImageIfExistInRequest($request);

            $store->fill([
                "photo_url" => \asset("storage/" . $path),
            ]);

            $store->saveOrFail();

            return $this->success(200, "Update successfully", $store);
        } catch (FileException $e) {
            return $this->error(500, "Something went wrong", $e->getMessage());
        } catch (\Throwable $th) {
            $isDeleted = $this->removeMedia($path);
            \throw_if(!$isDeleted, Throwable::class, "Cannot delete the file");
            return $this->error(500, "Something went wrong", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        $this->authorize('delete', $store);
        $store->deleteOrFail();

        return $this->success(200, "Delete successfully", null);
    }


    /**
     * Store image file or based64 file if any in request
     * 
     * @param Request $request
     * @return string $filePath
     * @throws FileException
     */
    private function storeImageIfExistInRequest(Request $request)
    {
        $path = "";
        if ($request->hasFile("store_image")) {
            $file = $request->file("store_image");
            $path = $this->storeMediaAsFile($file, self::$dirName);
            \throw_if(!$path, FileException::class, 'Error occur while uploading file');
        } elseif ($request->store_image_base64) {
            $file = $request->store_image_base64;
            $path = $this->storeMediaAsBased64($file, self::$dirName);
            \throw_if(!$path, FileException::class, 'Error occur while uploading file');
        }

        return $path;
    }
}
