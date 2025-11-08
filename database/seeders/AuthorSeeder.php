<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'name' => $faker->name,
                'bio' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Author::insert($data);
    }
}
