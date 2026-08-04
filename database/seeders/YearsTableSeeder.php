<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class YearsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Не вказано',
            '1940',
            '1955',
            '1990'
        ];

        foreach ($titles as $title) {
            // firstOrCreate запобігає помилкам дублювання
            Year::firstOrCreate(
                ['title' => $title],
                ['slug' => Str::slug($title)]
            );
        }
    }
}
