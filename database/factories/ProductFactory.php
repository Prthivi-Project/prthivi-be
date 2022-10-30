<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => $this->faker->word(),
            "description" => $this->faker->text(),
            "price" => $this->faker->numberBetween(50000, 100000000),
            "available" => $this->faker->boolean(50),
            "size" => $this->faker->randomLetter(),
            "fabric_composition" => $this->faker->word(),
        ];
    }
}
