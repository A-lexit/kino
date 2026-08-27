<?php
namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\TelegramSubscriber;
use App\Enums\FilmStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
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

        // Автоматично витягуємо дані користувача з повідомлення або callback
        $from = $data['message']['from'] ?? $data['callback_query']['from'] ?? null;
        $chatId = $data['message']['chat']['id'] ?? $data['callback_query']['message']['chat']['id'] ?? null;

        $subscriber = null;
        if ($from && $chatId) {
            $subscriber = TelegramSubscriber::updateOrCreate(
                ['chat_id' => $chatId],
                [
                    'username' => $from['username'] ?? null,
                    'first_name' => $from['first_name'] ?? null,
                ]
            );
        }

        // Обробка текстових повідомлень (наприклад, /start)
        if (isset($data['message'])) {
            $text = trim($data['message']['text'] ?? '');

            if ($subscriber && $subscriber->is_banned) {
                if ($text === '/start') {
                    $this->sendTelegramMessage($chatId, '⛔ На жаль, ваш акаунт заблоковано.');
                }
                return response('OK', 200);
            }

            if ($text === '/start') {
                $this->sendTelegramMessage($chatId, "👋 Вітаємо, {$subscriber->first_name}!\nВи успішно підписалися на сповіщення.");
            }
        }

        // Обробка callback від кнопок
        if (isset($data['callback_query'])) {
            $callback = $data['callback_query'];
            $action   = $callback['data'] ?? '';
            $messageId = $callback['message']['message_id'] ?? null;

            if ($subscriber && $subscriber->is_banned) {
                Http::post('https://api.telegram.org/bot' . config('services.telegram.token') . '/answerCallbackQuery', [
                    'callback_query_id' => $callback['id'],
                    'text' => '⛔ Ваш акаунт заблоковано в адмінці.',
                    'show_alert' => true
                ]);
                return response('OK', 200);
            }

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

    private function sendTelegramMessage($chatId, $text)
    {
        Http::post('https://api.telegram.org/bot' . config('services.telegram.token') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

}
