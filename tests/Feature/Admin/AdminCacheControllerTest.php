<?php
namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdminCacheControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Використовуємо фабрику замість ручного створення

        // Було: User::factory()->create(['is_admin' => 1]);
        // Проблема: 'role' лишався 'user' за замовчуванням, а модель
        // автоматично перераховує is_admin З role — тому is_admin=1
        // миттєво скидався назад до 0, і AdminMiddleware блокував 404-кою.
        $this->actingAs(User::factory()->admin()->create());

    }

    public function test_clear_method_executes_artisan_command_and_redirects_back(): void
    {
        // Перехоплюємо виклик команди
        Artisan::shouldReceive('call')
            ->once()
            ->with('cache:clear')
            ->andReturn(0);

        // $this->from() імітує попередню сторінку, звідки прийшов користувач
        $response = $this->from('/admin')
            ->get(route('admin.cache.clear'));

        $response->assertRedirect('/admin')
            ->assertSessionHas('message', 'Кеш очищено');
    }

}
