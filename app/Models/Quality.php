<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Quality extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug'];


    public function films()
    {
        return $this->hasMany(Film::class, 'quality_id', 'id');
    }

}
