<?php
namespace Database\Seeders;

use App\Models\Caption;
use Illuminate\Database\Seeder;

class CaptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Англійські',
            'Українські',
            'Російські',
            'Ютуб',
            'Немає',
        ];

        foreach ($titles as $title) {
            Caption::create([
                'title' => $title,
            ]);
        }
    }
}
