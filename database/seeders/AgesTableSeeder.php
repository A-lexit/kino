<?php
namespace Database\Seeders;

use App\Models\Age;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Не вказано',
            '18+',
            '16+',
            '14+',
            '12+',
            '6+',
            '3+',
            '0+',
            'R',
            'TV-14',
            'TV-MA',
            'TV-PG',
            'TV-G',
            'PG',
            'Невідомо',
        ];

        foreach ($titles as $title) {
            Age::firstOrCreate([
                'title' => $title,
            ], [
                'slug' => Str::slug($title),
            ]);
        }
    }
}
