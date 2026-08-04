<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Не вказано',
            'Фільми',
            'Серіали',
            'Мультики',
            'Мультсеріали',
        ];

        foreach ($titles as $title) {
            Category::create([
                'title' => $title,
            ]);
        }
    }
}
