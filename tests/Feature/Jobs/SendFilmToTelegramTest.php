<?php

namespace Tests\Feature\Jobs;

use App\APIs\TelegramService;
use App\Jobs\SendFilmToTelegram;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SendFilmToTelegramTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_film_to_telegram(): void
    {
        $film = Film::factory()->create();

        $telegram = $this->mock(
            TelegramService::class,
            function (MockInterface $mock) use ($film) {
                $mock->shouldReceive('sendFilm')
                    ->once()
                    ->with(Mockery::on(function (Film $argument) use ($film) {
                        return $argument->id === $film->id;
                    }));
            }
        );

        $job = new SendFilmToTelegram($film->id);

        $job->handle($telegram);
    }

    public function test_job_does_nothing_when_film_does_not_exist(): void
    {
        $telegram = $this->mock(
            TelegramService::class,
            function (MockInterface $mock) {
                $mock->shouldNotReceive('sendFilm');
            }
        );

        $job = new SendFilmToTelegram(999999);

        $job->handle($telegram);
    }

    public function test_job_can_be_dispatched_to_queue(): void
    {
        Queue::fake();

        $film = Film::factory()->create();

        SendFilmToTelegram::dispatch($film->id);

        Queue::assertPushed(SendFilmToTelegram::class);
    }
}
