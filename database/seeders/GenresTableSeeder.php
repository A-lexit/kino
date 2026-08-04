<?php
namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $titles = [
            'Не вказано',
            'Комедія',
            'Сімейний',
            'Спортивний',
            'Молодіжний',
            'Пародія',
            'Пригоди',
            'Вестерн',
            'Екранізація',
            'Бойовик',
            'Історичний',
            'Екшн',
            'Жахи',
            'Драма',
            'Детектив',
            'Кримінал',
            'Фентезі',
            'Фантастика',
            'Трилер',
            'Автобіографія',
        ];

        foreach ($titles as $title) {
            Genre::create([
                'title' => $title,
            ]);
        }
    }
}
