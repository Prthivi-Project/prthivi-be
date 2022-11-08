<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->name();
        return [
            "name" => $name,
            "description" => $this->faker->sentence(),
            'slug' => Str::slug($name . Str::random(4)),
            "address" => $this->faker->address(),
            "photo_url" => $this->faker->imageUrl(),
            "map_location" => $this->faker->word(),
        ];
    }
}
