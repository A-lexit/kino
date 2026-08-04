<?php
namespace Tests\Feature\APIs;

use App\APIs\TelegramService;
use App\Enums\FilmStatus;
use App\Models\Film;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramServiceTest extends TestCase
{
    private function makeFilm(): Film
    {
        $film = new Film();

        $film->id = 10;
        $film->title = 'Avatar';
        $film->tmdb_poster = '/poster.jpg';
        $film->publish_status = FilmStatus::Draft;

        return $film;
    }


    public function test_send_film_sends_request_to_telegram(): void
    {
        config([
            'services.telegram.token' => 'test-token',
            'services.telegram.chat_id' => '123456',
        ]);

        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
            ], 200),
        ]);

        $film = $this->makeFilm();

        app(TelegramService::class)->sendFilm($film);

        Http::assertSent(function ($request) {

            return $request->url()
                === 'https://api.telegram.org/bottest-token/sendPhoto'

                && $request['chat_id'] === '123456'

                && $request['photo']
                === 'https://image.tmdb.org/t/p/w500/poster.jpg'

                && str_contains(
                    $request['caption'],
                    'Avatar'
                );
        });
    }


    public function test_send_film_contains_inline_buttons(): void
    {
        config([
            'services.telegram.token' => 'test-token',
            'services.telegram.chat_id' => '123456',
        ]);

        Http::fake();

        $film = $this->makeFilm();

        app(TelegramService::class)->sendFilm($film);

        Http::assertSent(function ($request) {

            $markup = json_decode(
                $request['reply_markup'],
                true
            );

            return $markup['inline_keyboard'][0][0]['callback_data']
                === 'publish_10'

                &&

                $markup['inline_keyboard'][1][0]['callback_data']
                === 'draft_10';
        });
    }

}
