<?php
namespace App\Excel\Imports;

use App\Models\Actor;
use App\Models\Age;
use App\Models\Category;
use App\Models\Country;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Quality;
use App\Models\Rating;
use App\Models\Year;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class FilmsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public array $warnings = [];
    public int $successCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;
    public int $failCount = 0;

    protected array $cache = [];
    protected string $mode;

    public function __construct(string $mode = 'soft')
    {
        $this->mode = $mode;
    }

    public function model(array $row)
    {
        try {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                $this->failCount++;
                return null;
            }

            $category = $this->resolveStrict(Category::class, $row['category'] ?? null, 'категорія');
            $year = $this->resolveStrict(Year::class, $row['year'] ?? null, 'рік');
            $age = $this->resolveStrict(Age::class, $row['age'] ?? null, 'вік');
            $quality = $this->resolveStrict(Quality::class, $row['quality'] ?? null, 'якість');
            $rating = $this->resolveStrict(Rating::class, $row['rating'] ?? null, 'рейтинг');

            $existingFilm = Film::where('title', $title)->first();

            $isMergeMode = in_array($this->mode, ['update_merge', 'insert_update_merge']);
            $isUpdateMode = in_array($this->mode, ['update_only', 'insert_update', 'update_merge', 'insert_update_merge']);

            $film = null;

            if ($existingFilm) {
                if ($isUpdateMode) {
                    if ($isMergeMode) {
                        // Оновлюємо лише ті поля, які зараз порожні в БД
                        $attributes = [
                            'origin_title' => !empty($existingFilm->origin_title) ? $existingFilm->origin_title : ($row['origin_title'] ?? null),
                            'description' => !empty($existingFilm->description) ? $existingFilm->description : ($row['description'] ?? null),
                            'category_id' => $existingFilm->category_id ?: ($category?->id ?? null),
                            'year_id' => $existingFilm->year_id ?: ($year?->id ?? null),
                            'age_id' => $existingFilm->age_id ?: ($age?->id ?? null),
                            'quality_id' => $existingFilm->quality_id ?: ($quality?->id ?? null),
                            'rating_id' => $existingFilm->rating_id ?: ($rating?->id ?? null),
                        ];
                    } else {
                        // Повний перезапис з файлу
                        $attributes = [
                            'origin_title' => $row['origin_title'] ?? null,
                            'description' => $row['description'] ?? null,
                            'category_id' => $category?->id,
                            'year_id' => $year?->id,
                            'age_id' => $age?->id,
                            'quality_id' => $quality?->id,
                            'rating_id' => $rating?->id,
                        ];
                    }

                    $existingFilm->update($attributes);
                    $film = $existingFilm;
                    $this->updatedCount++;
                } else {
                    $this->skippedCount++;
                    return null;
                }
            } else {
                if (in_array($this->mode, ['update_only', 'update_merge'])) {
                    $this->skippedCount++;
                    return null;
                } else {
                    $attributes = [
                        'title' => $title,
                        'origin_title' => $row['origin_title'] ?? null,
                        'description' => $row['description'] ?? null,
                        'slug' => Str::slug($title . '-' . Str::random(5)),
                        'category_id' => $category?->id,
                        'year_id' => $year?->id,
                        'age_id' => $age?->id,
                        'quality_id' => $quality?->id,
                        'rating_id' => $rating?->id,
                        'publish_status' => 'draft',
                    ];
                    $film = Film::create($attributes);
                    $this->successCount++;
                }
            }

            if ($film) {
                // Для режимів merge використовуємо syncWithoutDetaching, щоб додати нові зв'язки без видалення старих
                $this->syncFreeform($film, 'actors', $row['actors'] ?? '', Actor::class, $isMergeMode);
                $this->syncFreeform($film, 'genres', $row['genres'] ?? '', Genre::class, $isMergeMode);
                $this->syncFreeform($film, 'countries', $row['countries'] ?? '', Country::class, $isMergeMode);
            }

            return $film;

        } catch (Throwable $e) {
            $this->failCount++;

            $this->warnings[] = [
                'title' => $title ?? 'Невідомий фільм',
                'message' => $e->getMessage(),
            ];

            \Log::error('Помилка імпорту фільму', [
                'title' => $title ?? null,
                'row' => $row,
                'exception' => $e,
            ]);

            return null;
        }
    }

    public function onError(Throwable $e)
    {
        $this->failCount++;

        $this->warnings[] = [
            'message' => $e->getMessage(),
        ];

        \Log::error('Помилка імпорту Excel', [
            'exception' => $e,
        ]);
    }

    protected function resolveStrict(string $modelClass, ?string $value, string $label)
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $cacheKey = $modelClass . '_' . $value;
        if (isset($this->cache[$cacheKey])) return $this->cache[$cacheKey];

        $record = $modelClass::where('title', $value)->first();
        if (!$record) {
            if ($this->mode === 'strict') {
                throw new \Exception("Суворий режим: не знайдено {$label} \"{$value}\" в базі даних.");
            }
            $this->warnings[] = "Не знайдено {$label}: \"{$value}\" — поле залишено порожнім.";
        }

        $this->cache[$cacheKey] = $record;
        return $record;
    }

    protected function syncFreeform(Film $film, string $relation, string $rawValue, string $modelClass, bool $merge = false): void
    {
        $names = array_filter(array_map('trim', explode(',', $rawValue)));
        if (empty($names)) return;

        $ids = [];
        $field = $modelClass === Actor::class ? 'name' : 'title';

        foreach ($names as $name) {
            $cacheKey = $modelClass . '_' . $name;
            if (!isset($this->cache[$cacheKey])) {
                $this->cache[$cacheKey] = $modelClass::firstOrCreate([$field => $name]);
            }
            $ids[] = $this->cache[$cacheKey]->id;
        }

        if ($merge && !$film->wasRecentlyCreated) {
            $film->{$relation}()->syncWithoutDetaching($ids);
        } else {
            $film->{$relation}()->sync($ids);
        }
    }
}
