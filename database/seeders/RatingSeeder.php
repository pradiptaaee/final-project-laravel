<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $faker = Faker::create();
        $total = 500000;
        $batchSize = 5000; 

        echo "🚀 Mulai seeding rating data...\n";

        for ($i = 0; $i < $total; $i += $batchSize) {
            $data = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $data[] = [
                    'book_id' => $faker->numberBetween(1, 100000),
                    'user_id' => $faker->numberBetween(1, 1000), 
                    'rating' => $faker->numberBetween(1, 10),
                    'review' => $faker->optional()->sentence(),
                ];
            }

            DB::table('ratings')->insert($data);

            echo "✅ Batch " . (($i / $batchSize) + 1) . " selesai (" . ($i + $batchSize) . "/" . $total . ")\n";
        }

        echo "🎉 Selesai membuat 500.000 data rating!\n";
    }
}
