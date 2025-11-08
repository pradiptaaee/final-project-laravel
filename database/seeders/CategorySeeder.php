<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];
        for ($i = 0; $i < 300; $i++) {
            $data[] = [
                'name' => $faker->name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Category::insert($data);
    }
}
