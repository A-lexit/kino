<?php
namespace App\Http\Controllers;

use App\Models\Film;
use App\Enums\FilmStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController0 extends Controller
{
    public function handle(Request $request)
    {
        // === ЗАХИСТ СЕКРЕТОМ ===
        $expectedSecret = config('services.telegram.webhook_secret');
        $providedSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');

        if (!$expectedSecret || !hash_equals($expectedSecret, (string) $providedSecret)) {
            Log::warning('Invalid Telegram webhook secret attempt');
            abort(403, 'Invalid webhook secret');
        }

        $data = $request->all();

        // Обробка callback від кнопок
        if (isset($data['callback_query'])) {
            $callback = $data['callback_query'];
            $action   = $callback['data'] ?? '';
            $chatId   = $callback['message']['chat']['id'] ?? null;
            $messageId = $callback['message']['message_id'] ?? null;

            $filmId = null;
            $newStatus = null;
            $successText = '';

            if (str_starts_with($action, 'publish_')) {
                $filmId = str_replace('publish_', '', $action);
                $newStatus = FilmStatus::Published;
                $successText = 'Фільм опубліковано ✅';
            } elseif (str_starts_with($action, 'draft_')) {
                $filmId = str_replace('draft_', '', $action);
                $newStatus = FilmStatus::Draft;
                $successText = 'Залишено у чернетках 📝';
            }

            if ($filmId && $newStatus) {
                $film = Film::find($filmId);

                if ($film) {
                    $film->update(['publish_status' => $newStatus]);
                    /*Cache::forget("film_{$film->slug}");*/    // Cache::forget тут не потрібен — FilmObserver::saved() вже це робить

                    // Відповідаємо Telegram, що кнопку оброблено
                    Http::post('https://api.telegram.org/bot' . config('services.telegram.token') . '/answerCallbackQuery', [
                        'callback_query_id' => $callback['id'],
                        'text' => $successText,
                        'show_alert' => false
                    ]);

                    // Оновлюємо кнопки
                    Http::post('https://api.telegram.org/bot' . config('services.telegram.token') . '/editMessageReplyMarkup', [
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    ['text' => $newStatus === FilmStatus::Published ? '✅ Опубліковано' : 'Опублікувати 🚀',
                                        'callback_data' => 'publish_' . $filmId],
                                    ['text' => $newStatus === FilmStatus::Draft ? '✅ Чернетка' : 'Чернетка 📝',
                                        'callback_data' => 'draft_' . $filmId],
                                ]
                            ]
                        ])
                    ]);
                }
            }
        }

        return response('OK', 200);
    }

}
