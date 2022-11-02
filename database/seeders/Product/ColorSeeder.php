<?php

namespace Database\Seeders\Product;

use App\Models\Product\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Color::factory()->createManyQuietly([
            [
                "color" => "krem",
                "hexa_code" => '#fff000',
            ],
            [
                "color" => "dongker",
                "hexa_code" => '#cfa831',
            ],
            [
                "color" => "toska",
                "hexa_code" => '#ac8da1',
            ],
        ]);
    }
}
