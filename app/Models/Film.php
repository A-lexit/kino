<?php
namespace App\Models;
/**
 * @method static \Illuminate\Database\Eloquent\Builder|static applySorting(string $sort)
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\FilmStatus;
use App\Traits\DateFormats;
use App\Traits\SortFilms;
use App\Enums\UserRole;


class Film extends Model
{
    protected static function booted(): void
    {
        static::saved(function ($film) {
            if (request()->has('related_films')) {
                $relatedIds = request()->input('related_films', []);

                $film->relatedFilms()->syncWithoutDetaching($relatedIds);

                foreach ($relatedIds as $relatedId) {
                    if ($relatedFilm = self::find($relatedId)) {
                        $peers = array_merge([$film->id], array_diff($relatedIds, [$relatedId]));
                        $relatedFilm->relatedFilms()->syncWithoutDetaching($peers);
                    }
                }
            }

        });
    }

    use HasFactory, DateFormats, SoftDeletes, SortFilms;

    protected $attributes = [
        /*'year_id'     => 1,*/
    ];

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
        'sort_order',
    ];

    protected $casts = [
        'is_featured'    => 'boolean',
        'tmdb_id'        => 'integer',
        'publish_status' => FilmStatus::class,
        'datepicker'     => \App\Casts\DatePickerCast::class,
    ];

    public function state(): HasOne
    {
        return $this->hasOne(State::class, 'film_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'film_id');
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault([
            'slug'  => 'uncategorized',
            'title' => 'Без категорії'
        ]);
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function duration(): BelongsTo
    {
        return $this->belongsTo(Duration::class, 'duration_id');
    }

    public function quality(): BelongsTo
    {
        return $this->belongsTo(Quality::class, 'quality_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class, 'rating_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function age(): BelongsTo
    {
        return $this->belongsTo(Age::class, 'age_id');
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

    public function relatedFilms(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'film_related_film', 'film_id', 'related_film_id');
    }


    public function seoDescription(): string
    {
        if (!empty($this->description)) {
            return \Illuminate\Support\Str::limit(strip_tags($this->description), 160);
        }

        $parts = array_filter([
            $this->category?->title,
            $this->year ? $this->year->title . ' рік' : null,
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
        if (
            $value === FilmStatus::Published->value
            && is_null($this->category_id)
        ) {
            $this->publish_status = FilmStatus::Draft;
            $this->save();

            return;
        }

        $this->publish_status = $value === FilmStatus::Published->value
            ? FilmStatus::Published
            : FilmStatus::Draft;

        $this->save();
    }


    public function toggleFeatured(mixed $value): void
    {
        if ($value) {
            $this->is_featured = true;
        } else {
            $this->is_featured = false;
        }

        $this->save();
    }


    public function getDisplayDateAttribute(): string
    {
        return empty($this->attributes['datepicker'])
            ? $this->createdAtFormatter
            : (string)$this->datepicker;
    }


    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration) {
            return null;
        }

        $total = (int) $this->duration->title;
        $hours = intdiv($total, 60);
        $minutes = $total % 60;

        if ($total < 60) {
            return "{$total} хв";
        }

        return $minutes > 0 ? "{$hours} год {$minutes} хв" : "{$hours} год";
    }


    public function getUrlAttribute(): string
    {
        return route('single', [
            'category' => $this->category->slug,
            'slug' => $this->slug,
        ]);
    }


    public function scopePublished($query)
    {
        return $query->where('publish_status', FilmStatus::Published);
    }

    
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        return match ($user->role) {
            UserRole::Admin,
            UserRole::Editor,
            UserRole::Viewer => $query,

            default => $query->whereRaw('1 = 0'),
        };
    }

}
