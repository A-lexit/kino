<?php
namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Завершений',
            'Незавершений',
            'Невідомо',
        ];

        foreach ($titles as $title) {
            Status::create([
                'title' => $title,
            ]);
        }
    }
}
