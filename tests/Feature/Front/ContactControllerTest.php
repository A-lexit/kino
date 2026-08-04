<?php

namespace Tests\Feature\Front;

use App\Mail\TestMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_method_sends_email_and_returns_view(): void
    {
        Mail::fake();

        Config::set('mail.test_recipient', 'test@example.com');

        $response = $this->get(route('mail.send'));

        $response->assertOk();
        $response->assertViewIs('mail.send');

        Mail::assertSent(TestMail::class);
    }
}
