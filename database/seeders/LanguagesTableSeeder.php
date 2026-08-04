<?php
namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $titles = [
            'Не вказано',
            'Українська',
            'Російська',
            'Англійська',
            'Українська (СТБ)',
            'Українська (ICTV)',
            'Українська (НТН)',
            'Українська (Інтер)',
            'Українська (ТРК Україна)',
            'Українська (ТЕТ)',
            'Українська (QTV)',
            'Українська (НЛО TV)',
            'Українська (Новий канал)',
            'Українська (К1)',
            'Українська (М1)',
            'Українська (1+1)',
            'Українська (2+2)',
            'Українська (Cine+)',
            'Українська (Paramount Comedy)',
            'Українська (Netflix)',
            'Українська (Line-in)',
            'Українська (Цікава Ідея)',
            'Українська (AMC)',
            'Українська (LeDoyen)',
            'Українська (ТакТребаПродакш)',
            'Українська (Postmodern)',
            'Українська (Кіно)',
            'Українська (Tretyakoff production)',
            'Українська (AniUA)',
            'Українська (Pie Post Production)',
            'Українська (Cinema Sound Production)',
            'Українська (КІТ)',
            'Українська (Омікрон(Hurtom))',
            'Українська (SkomUA)',
            'Українська (DniproFilm)',
            'Українська (HDrezka Studio)',
            'Українська (КІНОТА)',
            'Українська (UATeam)',
            'Українська ( simpsonsua.tv)',
            'Українська (Колодій)',
            'Українська (AAA-Sound)',
            'Українська (Багатоголосий закадровий)',
            'Українська (Двоголосий)',
            'Українська (Одноголосий)',
        ];


        foreach ($titles as $title) {
            Language::create([
                'title' => $title,
            ]);
        }
    }
}
