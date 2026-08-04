<?php
namespace Tests\Unit\View\Composers;

use App\Http\View\Composers\SettingsComposer;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class SettingsComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_compose_passes_settings_and_current_date_to_view(): void
    {
        Cache::flush();

        $settings = Setting::factory()->create();

        $view = Mockery::mock(View::class);

        $view->shouldReceive('with')
            ->once()
            ->with(Mockery::on(function ($data) use ($settings) {

                return
                    $data['settings']->is($settings)
                    && $data['currentDate'] instanceof \Illuminate\Support\Carbon;
            }));

        $composer = new SettingsComposer();

        $composer->compose($view);
        $this->addToAssertionCount(1);
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
