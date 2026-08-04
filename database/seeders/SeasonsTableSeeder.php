<?php
namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Сезон 1',
            'Сезон 1-2',
            'Сезон 1-3',
            'Сезон 1-4',
            'Сезон 1-5',
            'Сезон 1-6',
            'Сезон 1-7',
            'Сезон 1-8',
            'Сезон 1-9',
            'Сезон 1-10',
            'Сезон 1-11',
            'Сезон 1-12',
            'Сезон 1-13',
            'Сезон 1-14',
            'Сезон 1-15',
            'Сезон 1-16',
            'Сезон 1-17',
            'Сезон 1-18',
            'Сезон 1-19',
            'Сезон 1-20',
        ];

        foreach ($titles as $title) {
            Season::create([
                'title' => $title,
            ]);
        }
    }
}
