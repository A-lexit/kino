<?php

namespace App\Http\View\Composers;

use App\APIs\CurrencyService;
use Illuminate\View\View;

class CurrencyComposer
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    //кеш вже є в сервісі
    public function compose(View $view)
    {
        $view->with(
            'currency',
            $this->currencyService->getRates()
        );
    }

}
