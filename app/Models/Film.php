<?php
namespace App\Models;

use App\Enums\FilmStatus;
use App\Traits\DateFormats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Film extends Model
{
    use HasFactory, DateFormats, SoftDeletes;

    // Значення за замовчуванням для нових записів
    protected $attributes = [
        'category_id' => 1,
        'age_id'      => 1,
        'quality_id'  => 1,
        'rating_id'   => 1,
        'year_id'     => 1,
    ];

    // ====================== ВЛАСТИВОСТІ ======================
    protected $fillable = [
        'title',
        'slug',
        'origin_title',
        'description',
        'note',
        'subject',
        'body',
        'category_id',
        'season_id',
        'quality_id',
        'year_id',
        'rating_id',
        'status_id',
        'age_id',
        'duration_id',
        'thumbnail',
        'gal_image1',
        'gal_image2',
        'gal_image3',
        'gal_image4',
        'gal_image5',
        'trailer_youtube_id',
        'trailer_file',
        'other_actor',
        'publish_status',
        'is_featured',
        'tmdb_id',
        'tmdb_poster',
        'author_id',
        'user_id',
        'datepicker',
        'imdb_id',
        'imdb_rating',
    ];

    protected $casts = [
        'is_featured'    => 'boolean',
        'tmdb_id'        => 'integer',
        'publish_status' => FilmStatus::class,
        'datepicker'     => \App\Casts\DatePickerCast::class,
        'duration_id' => \App\Casts\DurationCast::class,
    ];

    // ====================== ВІДНОСИНИ ======================
    public function state(): HasOne
    {
        return $this->hasOne(State::class, 'film_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'film_id');
    }

    public function duration(): BelongsTo
    {
        // Виправлено зв'язок: вказуємо правильне поле 'duration_id'
        return $this->belongsTo(Duration::class, 'duration_id')->withDefault([
            'title' => 'Не вказано',
            'slug'  => 'ne-vkazano',
        ]);
    }

    public function quality(): BelongsTo
    {
        return $this->belongsTo(Quality::class, 'quality_id')->withDefault([
            'title' => 'Не вказано',
            'slug'  => 'ne-vkazano',
        ]);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id')->withDefault([
            'slug'  => 'uncategorized', // або 'default'
            'title' => 'Без категорії'
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault([
            'slug'  => 'uncategorized', // або 'default'
            'title' => 'Без категорії'
        ]);
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id')->withDefault([
            'title' => 'Не вказано',
            'slug'  => 'ne-vkazano',
        ]);
    }

    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class, 'rating_id')->withDefault([
            'title' => 'Не вказано',
            'slug'  => 'ne-vkazano',
        ]);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault([
            'slug'  => 'uncategorized', // або 'default'
            'title' => 'Без категорії'
        ]);
    }

    public function age(): BelongsTo
    {
        return $this->belongsTo(Age::class, 'age_id')->withDefault([
            'title' => 'Не вказано',
            'slug'  => 'ne-vkazano',
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'director_film', 'film_id', 'director_id');
    }

    public function composers(): BelongsToMany
    {
        return $this->belongsToMany(Composer::class, 'composer_film', 'film_id', 'composer_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_film', 'film_id', 'actor_id');
    }

    public function producers(): BelongsToMany
    {
        return $this->belongsToMany(Producer::class, 'film_producer', 'film_id', 'producer_id');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'film_genre', 'film_id', 'genre_id');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'film_language', 'film_id', 'language_id');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_film', 'film_id', 'country_id');
    }

    public function captions(): BelongsToMany
    {
        return $this->belongsToMany(Caption::class, 'caption_film', 'film_id', 'caption_id');
    }

    public function selections(): BelongsToMany
    {
        return $this->belongsToMany(Selection::class, 'film_selection', 'film_id', 'selection_id');
    }

    // ====================== ЛОГІКА ======================
    /**
     * Опис для <meta description>. Якщо адмін написав власний опис — беремо його
     * (обрізаний до 160 символів). Якщо опис порожній — автоматично збираємо
     * короткий опис зі структурованих даних (категорія, рік, жанри) —
     * вони завжди заповнені, на відміну від вільного тексту.
     */
    public function seoDescription(): string
    {
        if (!empty($this->description)) {
            return \Illuminate\Support\Str::limit(strip_tags($this->description), 160);
        }

        $parts = array_filter([
            $this->category->title ?? null,
            $this->year->title ? $this->year->title . ' рік' : null,
            $this->genres->pluck('title')->implode(', ') ?: null,
        ]);

        $summary = implode(', ', $parts);

        return \Illuminate\Support\Str::limit(
            "Дивіться онлайн «{$this->title}»" . ($summary ? " — {$summary}" : '') . '.',
            160
        );
    }


    public function togglePublishStatus(?string $value): void
    {
        if ($value === FilmStatus::Published->value) {
            $status = FilmStatus::Published;
        } else {
            $status = FilmStatus::Draft;
        }

        $this->publish_status = $status;
        $this->save();
    }



    public function toggleFeatured(mixed $value): void     //Читабильний варіант
    {
        if ($value) {
            $this->is_featured = true;
        } else {
            $this->is_featured = false;
        }

        $this->save();
    }


    // ====================== АКСЕСОРИ ======================
    public function getDisplayDateAttribute(): string
    {
        // Перевіряємо сире значення з бази: якщо воно порожнє, беремо createdAtFormatter
        return empty($this->attributes['datepicker'])
            ? $this->createdAtFormatter
            : (string)$this->datepicker;
    }


    // ====================== СКОУПИ ======================
    public function scopePublished($query)
    {
        return $query->where('publish_status', FilmStatus::Published);
    }


    public function scopeForUser($query, $user)
    {
        if ($user && in_array($user->role, [\App\Enums\UserRole::Admin, \App\Enums\UserRole::Viewer], true)) {
            return $query;
        }

        return $query->where('author_id', $user?->id);
    }

}
