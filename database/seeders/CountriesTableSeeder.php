<?php
namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Австралія',
            'Аруба',
            'Бельгія',
            'Болгарія',
            'Велика Британія',
            'Гонконг',
            'Ірландія',
            'Італія',
            'Канада',
            'Китай',
            'Мальта',
            'Мексика',
            'Німеччина',
            'Нова Зеландія',
            'США',
            'Україна',
            'Франція',
            'Чехія',
            'Швейцарія',
            'Японія',
            'Невідомо',
        ];

        foreach ($titles as $title) {
            Country::create([
                'title' => $title,
            ]);
        }
    }
}
