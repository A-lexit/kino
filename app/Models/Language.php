<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Language extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug'];


    public function films()
    {
        return $this->belongsToMany(Film::class, 'film_language', 'language_id', 'film_id');
    }

}
