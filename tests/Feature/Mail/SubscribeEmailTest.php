<?php
namespace Tests\Feature\Mail;

use App\Mail\SubscribeEmail;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_email_builds_correctly(): void
    {
        $subscription = Subscription::factory()->create();

        $mail = new SubscribeEmail($subscription);

        $mail->assertHasSubject('Subscriber');

        $mail->assertSeeInHtml($subscription->token);
    }

    public function test_subscription_is_available_in_view(): void
    {
        $subscription = Subscription::factory()->create();

        $mail = new SubscribeEmail($subscription);

        $this->assertSame(
            $subscription->id,
            $mail->subs->id
        );
    }

}
