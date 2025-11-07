<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author_id' => Author::inRandomOrder()->first()?->id ?? Author::factory(),
            'category_id' => category::inRandomOrder()->first()?->id ?? Category::factory(),
            'publication_year' => $this->faker->year(),
            'price' => $this->faker->numberBetween(10000, 200000),
        ];
    }
}
