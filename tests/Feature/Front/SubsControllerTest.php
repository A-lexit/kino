<?php
namespace Tests\Feature\Front;

use App\Jobs\SendSubscribeEmail;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe(): void
    {
        Queue::fake();

        $response = $this->from('/')
            ->post(route('subscribe'), [
                'email' => 'test@example.com',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('status', 'Перевірте вашу пошту!');

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'test@example.com',
        ]);

        $subscription = Subscription::where('email', 'test@example.com')->first();

        $this->assertNotNull($subscription->token);

        Queue::assertPushed(SendSubscribeEmail::class);
    }

    public function test_subscription_requires_valid_email(): void
    {
        $response = $this->from('/')
            ->post(route('subscribe'), [
                'email' => 'invalid-email',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');

        $this->assertDatabaseCount('subscriptions', 0);
    }


    public function test_user_can_verify_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'token' => 'verify-token',
        ]);

        $response = $this->get(route('subscribe.verify', 'verify-token'));

        $response->assertRedirect('/');
        $response->assertSessionHas('status', 'Вашу пошту підтверджено! Дякуємо!');

        $subscription->refresh();

        $this->assertNull($subscription->token);
    }


    public function test_verify_returns_404_for_invalid_token(): void
    {
        $response = $this->get(route('subscribe.verify', 'wrong-token'));

        $response->assertNotFound();
    }

}
