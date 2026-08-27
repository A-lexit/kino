<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('position');
    }

    /**
     * Готовий масив пунктів меню для рендеру: [['name'=>..,'url'=>..,'match'=>..], ...]
     */
    public function resolvedItems(): array
    {
        return $this->items()
            ->with('category')
            ->get()
            ->map(fn (MenuItem $item) => $item->resolve())
            ->filter() // прибирає null (биті/видалені категорії)
            ->values()
            ->all();
    }

}
