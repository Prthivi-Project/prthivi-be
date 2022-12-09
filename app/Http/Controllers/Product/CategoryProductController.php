<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryAttachRequest;
use App\Models\Product;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoryProductController extends Controller
{
    use ResponseFormatter;

    public function attachCategoriesProduct(CategoryAttachRequest $request, $productId)
    {
        $product = Product::find($productId);
        \abort_if(!$product, 404, "Product not found");

        $this->authorize("attachCategories", $product);

        try {
            $product->categories()->attach($request->validated()["categories"]);
            return $this->success(201, "Categories has been attached to product", $product->load("categories"));
        } catch (\Throwable $th) {
            throw new BadRequestHttpException($th->getMessage());
        }
    }

    public function detachCategoryProduct(CategoryAttachRequest $request, $productId)
    {
        $product = Product::find($productId);
        \abort_if(!$product, 404, "Product not found");

        $this->authorize("attachCategories", $product);
        try {
            $product->categories()->detach($request->validated()["categories"]);
            return $this->success(201, "Categories has been attached to product", $product->load("categories"));
        } catch (\Throwable $th) {
            throw new BadRequestHttpException($th->getMessage());
        }
    }
}
