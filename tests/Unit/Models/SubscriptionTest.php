<?php
namespace Tests\Unit\Models\Subscription;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_creates_new_subscription(): void
    {
        $subscription = Subscription::add('test@example.com');

        $this->assertInstanceOf(
            Subscription::class,
            $subscription
        );

        $this->assertSame(
            'test@example.com',
            $subscription->email
        );

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'test@example.com',
        ]);
    }


    public function test_generate_token_creates_token_and_saves_it(): void
    {
        $subscription = Subscription::factory()->create([
            'token' => null,
        ]);

        $subscription->generateToken();

        $subscription->refresh();

        $this->assertNotNull(
            $subscription->token
        );

        $this->assertSame(
            10,
            strlen($subscription->token)
        );

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'token' => $subscription->token,
        ]);
    }


    public function test_generate_token_changes_existing_token(): void
    {
        $subscription = Subscription::factory()->create([
            'token' => 'abcdefghij',
        ]);

        $oldToken = $subscription->token;

        $subscription->generateToken();

        $subscription->refresh();

        $this->assertNotSame(
            $oldToken,
            $subscription->token
        );
    }


    public function test_remove_deletes_subscription(): void
    {
        $subscription = Subscription::factory()->create();

        $subscription->remove();

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $subscription->id,
        ]);
    }

}
