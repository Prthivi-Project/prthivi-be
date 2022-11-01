<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ["name" => "TOP", "created_at" => now(), "updated_at" => now()],
            ["name" => "TEES AND TANKS", "created_at" => now(), "updated_at" => now()],
            ["name" => "SHIRTS", "created_at" => now(), "updated_at" => now()],
            ["name" => "DRESSES", "created_at" => now(), "updated_at" => now()],
            ["name" => "JACKETS", "created_at" => now(), "updated_at" => now()],
            ["name" => "KNITWEAR", "created_at" => now(), "updated_at" => now()],
            ["name" => "JUMPSUITS", "created_at" => now(), "updated_at" => now()],
            ["name" => "BOTTOM", "created_at" => now(), "updated_at" => now()],
            ["name" => "INTIMATES", "created_at" => now(), "updated_at" => now()],
            ["name" => "SHORTS", "created_at" => now(), "updated_at" => now()],
            ["name" => "JEANS", "created_at" => now(), "updated_at" => now()],
            ["name" => "SKIRTS", "created_at" => now(), "updated_at" => now()],
        ];

        Category::factory()->create($data);
    }
}
