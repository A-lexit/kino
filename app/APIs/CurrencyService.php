<?php
namespace App\APIs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public function getRates()
    {
        return Cache::remember('currency_rates', now()->addHours(3), function () {
            try {
                $response = Http::timeout(5)->get(
                    'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json'
                );

                if ($response->successful()) {
                    $data = $response->json();

                    return collect($data)
                        ->whereIn('cc', ['USD', 'EUR'])
                        ->mapWithKeys(function ($item) {
                            return [$item['cc'] => $item['rate']];
                        });
                }

                return null;
            } catch (\Exception $e) {
                Log::warning('Не вдалося отримати курс валют з НБУ: ' . $e->getMessage());
                return null;
            }
        });
    }
}
