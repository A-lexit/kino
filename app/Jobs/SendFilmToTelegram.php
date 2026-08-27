<?php
namespace App\Jobs;

use App\APIs\TelegramService;
use App\Models\Film;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFilmToTelegram implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $filmId
    ) {}

    public function handle(TelegramService $telegram): void
    {
        $film = Film::find($this->filmId);

        if (!$film) {
            return;
        }

        $telegram->sendFilm($film);
    }

}
