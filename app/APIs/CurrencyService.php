<?php
namespace App\APIs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public function getRates()
    {
        return Cache::remember('currency_rates', 3600, function () {

            $response = Http::get(
                'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json'
            );

            if ($response->successful()) {

                $data = $response->json();

                return collect($data)
                    ->whereIn('cc', ['USD', 'EUR'])
                    ->mapWithKeys(function ($item) {

                        return [
                            $item['cc'] => $item['rate']
                        ];
                    });
            }
            return null;
        });
    }
}
