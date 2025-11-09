<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $total = 97000; // total buku
        $batchSize = 3000; // 2000 per batch biar cepat tapi aman di RAM

        echo "🚀 Mulai seeding books data...\n";

        for ($i = 0; $i < $total; $i += $batchSize) {
            $data = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $data[] = [
                    'title' => $faker->sentence(3),
                    'isbn' => $faker->unique()->isbn13(),
                    'author_id' => $faker->numberBetween(1, 100), // sesuai jumlah fake author
                    'category_id' => $faker->numberBetween(1, 300), // sesuai jumlah fake category
                    'publication_year' => $faker->year(),
                    'status' => $faker->randomElement(['available', 'rented', 'reserved']),
                    'location' => 'Main Store',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('books')->insert($data);

            echo "✅ Batch " . (($i / $batchSize) + 1) . " selesai (" . ($i + $batchSize) . "/" . $total . ")\n";
        }

        echo "🎉 Selesai membuat 100.000 data buku!\n";
    }
}
