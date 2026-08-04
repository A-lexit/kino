<?php
namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Не вказано',
            '4',
            '5',
            '10',
        ];

        foreach ($titles as $title) {
            Rating::create([
                'title' => $title,
            ], [
                'slug' => Str::slug($title),
            ]);
        }
    }
}
