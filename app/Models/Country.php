<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Country extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug'];

    public function films()
    {
        return $this->belongsToMany(Film::class, 'country_film', 'country_id', 'film_id');
    }

}
