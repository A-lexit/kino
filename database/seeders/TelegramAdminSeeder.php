<?php

namespace Database\Seeders;

use App\Models\TelegramSubscriber;
use Illuminate\Database\Seeder;

class TelegramAdminSeeder extends Seeder
{
    public function run(): void
    {
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($chatId) {
            TelegramSubscriber::updateOrCreate(
                ['chat_id' => $chatId],
                [
                    'first_name' => 'Admin',
                    'username' => 'Admin',
                    'is_banned' => false,
                ]
            );
        }
    }
}
