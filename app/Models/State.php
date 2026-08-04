<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class State extends Model
{
    use HasFactory;

    protected $fillable = ['likes', 'vviews', 'film_id'];
    public $timestamps = false;


    public function film()
    {
        return $this->belongsTo(Film::class);
    }

}
