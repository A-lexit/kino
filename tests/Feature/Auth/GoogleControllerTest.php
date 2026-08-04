<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use App\Enums\AuthProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;
use Mockery;

class GoogleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_authenticates_a_user_via_google()
    {
        // 1. Створюємо мок користувача з усіма необхідними даними
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-123');
        $socialiteUser->shouldReceive('getName')->andReturn('Test User'); // Додано name
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');

        // 2. Мокаємо фасад Socialite
        Socialite::shouldReceive('driver')
            ->with(AuthProvider::Google->value)
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andReturn($socialiteUser);

        // 3. Виконуємо запит
        $response = $this->get('/auth/google/callback');

        // 4. Перевіряємо
        $response->assertRedirect(config('app.url'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User', // Перевірка імені
            'provider' => AuthProvider::Google->value,
            'provider_id' => 'google-123',
        ]);
    }

    public function test_it_links_existing_user_to_google()
    {
        // Створюємо юзера через фабрику (переконайтеся, що фабрика заповнює 'name')
        $existingUser = User::factory()->create([
            'email' => 'link@example.com',
            'provider_id' => null
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-999');
        $socialiteUser->shouldReceive('getEmail')->andReturn('link@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Link User'); // Додано name

        Socialite::shouldReceive('driver')->with(AuthProvider::Google->value)->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);

        $this->get('/auth/google/callback');

        // Оновлюємо модель, щоб підтягнути дані з БД
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'provider_id' => 'google-999',
        ]);
    }

}
