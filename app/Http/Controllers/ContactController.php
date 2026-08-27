<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send()
    {
        $recipient = config('mail.test_recipient');

        abort_unless($recipient, 500, 'MAIL_TEST_RECIPIENT is not configured.');

        Mail::to($recipient)->send(new TestMail());

        return view('mail.send');
    }

}
