<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug'];


    public function films()
    {
        return $this->belongsToMany(Film::class, 'company_film', 'company_id', 'film_id');
    }

}


