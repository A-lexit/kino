<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug'];


    public function films()
    {
        return $this->hasMany(Film::class, 'category_id', 'id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_category', 'category_id', 'menu_id');
    }


    public function scopeFindBySlug($query, $slug)
    {
        return $query->where('slug', $slug)->firstOrFail();
    }


    public function isSeries(): bool
    {
        return in_array($this->slug, ['seriali', 'multseriali']);
    }

}
