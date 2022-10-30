<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\ProductImages;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(20)->create();


        $user  = User::factory()->create();
        Store::factory()
            ->for($user)
            ->has(
                Product::factory()
                    ->hasImages()
                    ->count(100)
            )
            ->create();
    }
}
