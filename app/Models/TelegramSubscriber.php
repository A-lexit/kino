<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramSubscriber extends Model
{
    protected $fillable = ['chat_id', 'username', 'first_name', 'is_banned'];


    public function getIsAdminAttribute(): bool
    {
        return (string) $this->chat_id === (string) env('TELEGRAM_CHAT_ID');
    }

}
