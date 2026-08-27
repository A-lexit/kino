<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'type', 'category_id', 'static_key', 'position'];

    public const STATIC_PAGES = [
        'home' => ['label' => 'Головна', 'route' => 'home'],
        'selections' => ['label' => 'Добірки', 'route' => 'selections.index'],
        'actors' => ['label' => 'Актори', 'route' => 'actors.index'],
        'directors' => ['label' => 'Режисери', 'route' => 'directors.index'],
        'genres' => ['label' => 'Жанри', 'route' => 'genres.index'],
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Готовий пункт меню: назва, URL, і патерни для request()->is(...),
     * якими підсвічується активний пункт у шапці.
     */
    public function resolve(): ?array
    {
        if ($this->type === 'category') {
            if (!$this->category) {
                return null;
            }

            $slug = $this->category->slug;

            return [
                'name' => $this->category->title,
                'url' => route('categories.show', ['slug' => $slug]),
                'is_patterns' => ["category/{$slug}", "{$slug}/*"],
            ];
        }

        if ($this->type === 'static' && isset(self::STATIC_PAGES[$this->static_key])) {
            $page = self::STATIC_PAGES[$this->static_key];

            $patterns = $this->static_key === 'home'
                ? ['/']
                : [$this->static_key, "{$this->static_key}/*"];

            return [
                'name' => $page['label'],
                'url' => route($page['route']),
                'is_patterns' => $patterns,
            ];
        }

        return null;
    }


    public function getTitle(): string
    {
        if ($this->type === 'category') {
            return $this->category?->title ?? 'Категорія видалена';
        }

        return self::STATIC_PAGES[$this->static_key]['label']
            ?? $this->static_key;
    }

}
