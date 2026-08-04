<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class Subscription extends Model
{
    use HasFactory;


    public static function add($email)
    {
        Log::info('Додається підписник: ' . $email);
        $sub = new static;
        $sub->email = $email;
        $sub->save();
        Log::info('Підписник доданий: ' . $sub->id);
        return $sub;
    }

    public function generateToken()
    {
        Log::info('Генерується токен для: ' . $this->email);
        $this->token = Str::random(10);
        $this->save();
        Log::info('Токен згенерований: ' . $this->token);
    }


    public function remove()
    {
        $this->delete();
    }

}
