<?php
namespace Tests\Feature\Jobs;

use App\Jobs\SendSubscribeEmail;
use App\Mail\SubscribeEmail;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendSubscribeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_subscription_email(): void
    {
        Mail::fake();
        Log::spy();

        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
        ]);

        $job = new SendSubscribeEmail($subscription);

        $job->handle();

        Mail::assertSent(SubscribeEmail::class, function ($mail) use ($subscription) {
            return $mail->hasTo($subscription->email);
        });

        Log::shouldHaveReceived('info')
            ->with('Надсилання електронної пошти почалося для: ' . $subscription->email)
            ->once();

        Log::shouldHaveReceived('info')
            ->with('Надсилання електронної пошти завершено для: ' . $subscription->email)
            ->once();
    }

}
