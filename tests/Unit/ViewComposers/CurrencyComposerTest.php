<?php
namespace Tests\Unit\View\Composers;

use App\APIs\CurrencyService;
use App\Http\View\Composers\CurrencyComposer;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class CurrencyComposerTest extends TestCase
{
    public function test_compose_passes_currency_to_view(): void
    {
        $currency = collect([
            'USD' => 41.50,
            'EUR' => 45.20,
        ]);

        $service = Mockery::mock(CurrencyService::class);

        $service->shouldReceive('getRates')
            ->once()
            ->andReturn($currency);

        $view = Mockery::mock(View::class);

        $view->shouldReceive('with')
            ->once()
            ->with('currency', $currency);

        $composer = new CurrencyComposer($service);

        $composer->compose($view);

        // Щоб PHPUnit не позначав тест як Risky
        $this->assertTrue(true);
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
