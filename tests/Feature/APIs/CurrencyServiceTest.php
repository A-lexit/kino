<?php
namespace Tests\Feature\APIs;

use App\APIs\CurrencyService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    public function test_get_rates_returns_usd_and_eur_rates(): void
    {
        Http::fake([
            'bank.gov.ua/*' => Http::response([
                [
                    'cc' => 'USD',
                    'rate' => 41.20,
                ],
                [
                    'cc' => 'EUR',
                    'rate' => 44.50,
                ],
                [
                    'cc' => 'PLN',
                    'rate' => 10.30,
                ],
            ], 200),
        ]);


        Cache::forget('currency_rates');

        $service = new CurrencyService();

        $rates = $service->getRates();

        $this->assertEquals([
            'USD' => 41.20,
            'EUR' => 44.50,
        ], $rates->toArray());
    }


    public function test_get_rates_returns_null_when_api_failed(): void
    {
        Http::fake([
            'bank.gov.ua/*' => Http::response([], 500),
        ]);

        Cache::forget('currency_rates');

        $service = new CurrencyService();

        $rates = $service->getRates();

        $this->assertNull($rates);
    }


    public function test_get_rates_uses_cache(): void
    {
        Http::fake([
            'bank.gov.ua/*' => Http::response([
                [
                    'cc' => 'USD',
                    'rate' => 41.20,
                ],
                [
                    'cc' => 'EUR',
                    'rate' => 44.50,
                ],
            ], 200),
        ]);


        Cache::forget('currency_rates');

        $service = new CurrencyService();

        $first = $service->getRates();

        $second = $service->getRates();

        Http::assertSentCount(1);

        $this->assertEquals(
            $first,
            $second
        );
    }

}
