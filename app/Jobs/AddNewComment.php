<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Comment;

class AddNewComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    protected $body;
    protected $subject;
    protected $film_id;
    protected $user_id;
    protected $status;
    private $Auth;

    public function __construct($subject, $body, $status, $film_id, $user_id)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->status = $status;
        $this->film_id = $film_id;
        $this->user_id = $user_id;
    }

    public function handle()
    {

        $comment = Comment::create([
            'subject' => $this->subject,
            'body' => $this->body,
            'status' => $this->status,
            'film_id' => $this->film_id,
            /*'user_id' => Auth::id(),*/
            'user_id' => $this->user_id,
        ]);
    }

}
