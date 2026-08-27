<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Caption extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug'];


    public function films()
    {
        return $this->belongsToMany(Film::class, 'caption_film', 'caption_id', 'film_id');
    }

}
