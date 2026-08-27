<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Duration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['title', 'slug', 'film_id'];


    public function films() {
        return $this->hasMany(Film::class, 'duration_id', 'id');
    }

}
