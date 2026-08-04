<?php
namespace App\Jobs;

use App\Mail\SubscribeEmail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscribeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function handle()
    {
        Log::info('Надсилання електронної пошти почалося для: ' . $this->subscription->email);
        // Надсилання електронної пошти
        Mail::to($this->subscription->email)->send(new SubscribeEmail($this->subscription));
        Log::info('Надсилання електронної пошти завершено для: ' . $this->subscription->email);
    }

}
