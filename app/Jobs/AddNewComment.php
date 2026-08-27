<?php

namespace App\Jobs;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(
        public string $subject,
        public string $body,
        public int $status,
        public int $film_id,
        public ?int $user_id = null // Змінено тип з int на ?int
    ) {}

    public function handle(): void
    {
        Comment::create([
            'subject' => $this->subject,
            'body'    => $this->body,
            'status'  => $this->status,
            'film_id' => $this->film_id,
            'user_id' => $this->user_id,
        ]);
    }

}
