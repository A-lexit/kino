<?php
namespace App\APIs;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function sendFilm($film)
    {
        Http::post(
            'https://api.telegram.org/bot'
            .config('services.telegram.token')
            .'/sendPhoto',
            [

                'chat_id' => config('services.telegram.chat_id'),

                'photo' =>
                    'https://image.tmdb.org/t/p/w500'.$film->tmdb_poster,

                'caption' =>
                    "🎬 <b>Новий фільм</b>\n\n".
                    "Назва: ".$film->title."\n".
                    "Статус: ".$film->publish_status->value,


                'parse_mode'=>'HTML',


                'reply_markup'=>json_encode([

                    'inline_keyboard'=>[

                        [

                            [
                                'text'=>'✅ Опублікувати',
                                'callback_data'=>'publish_'.$film->id
                            ]

                        ],

                        [

                            [
                                'text'=>'📝 Залишити чернеткою',
                                'callback_data'=>'draft_'.$film->id
                            ]

                        ]

                    ]

                ])

            ]
        );
    }

}
