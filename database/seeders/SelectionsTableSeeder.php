<?php
namespace Database\Seeders;

use App\Models\Selection;
use Illuminate\Database\Seeder;

class SelectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Зимові',
            'Тачки',
            'Поліція',
            'Спок',
            'Шпигуни',
            'Супергерої',
            'Космічні',
            'Французькі',
        ];

        foreach ($titles as $title) {
            Selection::create([
                'title' => $title,
            ]);
        }
    }
}
