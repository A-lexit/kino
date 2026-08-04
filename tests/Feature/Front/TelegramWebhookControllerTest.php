<?php
namespace Tests\Feature\Front;

use App\Enums\FilmStatus;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.telegram.webhook_secret' => 'secret123',
            'services.telegram.token' => 'telegram-token',
        ]);
    }


    public function test_webhook_rejects_invalid_secret(): void
    {
        $response = $this->postJson('/telegram/webhook', [], [
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong-secret',
        ]);

        $response->assertForbidden();
    }


    public function test_webhook_publish_film_from_callback(): void
    {
        Http::fake();

        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Draft,
        ]);

        $payload = [
            'callback_query' => [
                'id' => 'callback123',
                'data' => 'publish_' . $film->id,

                'message' => [
                    'chat' => [
                        'id' => 123456,
                    ],
                    'message_id' => 555,
                ],
            ],
        ];

        $response = $this->postJson(
            '/telegram/webhook',
            $payload,
            [
                'X-Telegram-Bot-Api-Secret-Token' => 'secret123',
            ]
        );

        $response->assertOk();
        $response->assertContent('OK');

        $film->refresh();

        $this->assertEquals(
            FilmStatus::Published,
            $film->publish_status
        );

        Http::assertSentCount(2);
    }


    public function test_webhook_draft_film_from_callback(): void
    {
        Http::fake();

        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Published,
        ]);

        $payload = [
            'callback_query' => [
                'id' => 'callback123',
                'data' => 'draft_' . $film->id,

                'message' => [
                    'chat' => [
                        'id' => 123456,
                    ],
                    'message_id' => 555,
                ],
            ],
        ];

        $response = $this->postJson(
            '/telegram/webhook',
            $payload,
            [
                'X-Telegram-Bot-Api-Secret-Token' => 'secret123',
            ]
        );

        $response->assertOk();

        $film->refresh();

        $this->assertEquals(
            FilmStatus::Draft,
            $film->publish_status
        );

        Http::assertSentCount(2);
    }


    public function test_webhook_returns_ok_for_unknown_film(): void
    {
        Http::fake();

        $payload = [
            'callback_query' => [
                'id' => 'callback123',
                'data' => 'publish_999999',

                'message' => [
                    'chat' => [
                        'id' => 123456,
                    ],
                    'message_id' => 555,
                ],
            ],
        ];

        $response = $this->postJson(
            '/telegram/webhook',
            $payload,
            [
                'X-Telegram-Bot-Api-Secret-Token' => 'secret123',
            ]
        );

        $response->assertOk();
        $response->assertContent('OK');

        Http::assertNothingSent();
    }


    public function test_webhook_returns_ok_for_unknown_action(): void
    {
        Http::fake();

        $payload = [
            'callback_query' => [
                'id' => 'callback123',
                'data' => 'something_wrong',

                'message' => [
                    'chat' => [
                        'id' => 123456,
                    ],
                    'message_id' => 555,
                ],
            ],
        ];

        $response = $this->postJson(
            '/telegram/webhook',
            $payload,
            [
                'X-Telegram-Bot-Api-Secret-Token' => 'secret123',
            ]
        );

        $response->assertOk();

        Http::assertNothingSent();
    }

}
