<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseFormatter;

    public function index()
    {
        $cat = Category::all();
        return $this->success(200, "Get all categories", $cat);
    }

    public function store(Request $request)
    {
        $this->authorize("create", Category::class);
        $request->validate([
            "name" => ["required", "string"],
        ]);

        $category = Category::create([
            "name" => $request->name
        ]);

        \abort_if(!$category, 500, "Category cannot created");

        return $this->success(201, "Berhasil menambahkan category", $category);
    }

    public function update(Request $request, $category)
    {
        $this->authorize("update", $category);

        $request->validate([
            "name" => ["required", "string"],
        ]);


        $category = $category->update([
            "name" => $request->name
        ]);

        \abort_if(!$category, 500, "Category cannot updated");

        return $this->success(200, "Berhasil update category", $category);
    }

    public function delete($category)
    {
        $category->delete();
        return $this->success(200, "Category deleted", null);
    }
}
