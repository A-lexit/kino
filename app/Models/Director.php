<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Director extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug'];


    public function films()
    {
        return $this->belongsToMany(Film::class, 'director_film', 'director_id', 'film_id');
    }

}
