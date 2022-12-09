<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseFormatter;

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
}
