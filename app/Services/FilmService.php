<?php
namespace App\Services;

use App\Media\FilmImageMedia;
use App\Models\Film;
use App\Http\Requests\FilmRequest;
use Illuminate\Http\Request;
use App\Media\FilmVideoResolver;

class FilmService
{
    public function __construct(
        protected FilmImageMedia $filmImageMedia,
        protected \App\Media\FilmVideoMedia $filmVideoMedia,
        protected \App\APIs\OmdbService $omdbService,
        protected FilmRelationService $relationService
    ) {}

    public function getFilmsForUser($user): array
    {
        $with = ['category:id,title,slug', 'actors:id,name'];

        $films = Film::with($with)->forUser($user)->latest('id')->paginate(50);
        $sdelfilms = Film::onlyTrashed()->forUser($user)->latest('id')->paginate(30);

        return compact('films', 'sdelfilms');
    }

    public function createFilm(FilmRequest $request): Film
    {
        // Беремо вже очищені та підготовлені FormRequest-ом дані
        $data = $request->validated();

        // Передаємо slug для генерації красивої назви файлу
        $this->filmImageMedia->uploadFilmImages($request, $data, null, $data['slug'] ?? null);

        $this->filmVideoMedia->uploadTrailer($request, $data);
        $this->handleTrailerYoutubeUrl($data);

        $film = Film::create($data);

        $this->afterSaveActions($film, $request);

        return $film;
    }


    public function updateFilm($id, FilmRequest $request): ?Film
    {
        $film = Film::find($id);
        if (!$film) {
            return null;
        }

        $data = $request->validated();

        // Передаємо slug при оновленні (беремо новий з даних або старий з моделі)
        $this->filmImageMedia->uploadFilmImages($request, $data, $film, $data['slug'] ?? $film->slug);

        $this->filmVideoMedia->uploadTrailer($request, $data, $film);
        $this->handleTrailerYoutubeUrl($data);

        $film->fill($data);
        $film->save();

        $this->afterSaveActions($film, $request);

        return $film;
    }

    public function deleteFilm($id): ?Film
    {
        $film = Film::withTrashed()->find($id);
        if (!$film) return null;

        $film->delete();

        return $film;
    }

    public function restoreFilm($id): ?Film
    {
        $film = Film::withTrashed()->find($id);
        if (!$film?->trashed()) {
            return null;
        }

        $this->ensureTitleExists($film);
        $film->restore();

        return $film;
    }

    public function restoreAllFilms(): int
    {
        $count = 0;

        // Обробка порціями по 100 штук для економії оперативної пам'яті
        Film::onlyTrashed()->chunk(100, function ($films) use (&$count) {
            Film::withoutEvents(function () use ($films, &$count) {
                foreach ($films as $film) {
                    $this->ensureTitleExists($film);
                    $film->restore();
                    $count++;
                }
            });
        });

        return $count;
    }

    public function forceDeleteFilm($id): ?Film
    {
        $film = Film::withTrashed()->find($id);
        if (!$film) return null;

        /*$this->filmImageMedia->deleteAll($film);*/
        $this->filmImageMedia->deleteFilmImages($film);

        $film->forceDelete();

        return $film;
    }

    public function forceDeleteAllFilms(): int
    {
        $count = 0;

        // Безпечне масове видалення без перевантаження RAM
        Film::onlyTrashed()->chunk(100, function ($films) use (&$count) {
            Film::withoutEvents(function () use ($films, &$count) {
                foreach ($films as $film) {
                    /*$this->filmImageMedia->deleteAll($film);*/
                    $this->filmImageMedia->deleteFilmImages($film);
                    $film->forceDelete();
                    $count++;
                }
            });
        });

        return $count;
    }

    public function findFilm($id): Film
    {
        return Film::findOrFail($id);
    }

    // ====================== Приватні методи ======================

    protected function ensureTitleExists(Film $film): void
    {
        if (empty($film->title)) {
            $film->title = 'Невідомий фільм ' . uniqid();
            $film->save();
        }
    }

    protected function afterSaveActions(Film $film, Request $request): void
    {
        // Якщо категорія не вибрана — фільм примусово залишається чернеткою,
        // незалежно від того, чи позначено "Опублікувати"
        if (empty($film->category_id) || $film->category_id === $this->uncategorizedCategoryId()) {
            $film->togglePublishStatus(null); // null !== 'published' → завжди Draft
        } else {
            $film->togglePublishStatus($request->get('publish_status'));
        }

        $film->toggleFeatured($request->get('is_featured'));
        $this->relationService->sync($film, $request->all());
    }

    protected function uncategorizedCategoryId(): ?int
    {
        return \App\Models\Category::where('slug', 'uncategorized')->value('id');
    }


    private function handleTrailerYoutubeUrl(array &$data): void
    {
        // trailer_youtube_url — тимчасове поле з форми (не колонка в БД),
        // конвертуємо в trailer_youtube_id перед збереженням
        if (empty($data['trailer_youtube_url'])) {
            unset($data['trailer_youtube_url']);
            return;
        }

        $id = FilmVideoResolver::extractYoutubeId($data['trailer_youtube_url']);
        if ($id) {
            $data['trailer_youtube_id'] = $id;
            // якщо вказали YouTube — власний файл більше не актуальний
            $data['trailer_file'] = null;
        }

        unset($data['trailer_youtube_url']);
    }


    public function fetchImdbRating(Film $film): ?Film
    {
        $result = $this->omdbService->fetchRating($film);

        if (is_null($result)) {
            return null;
        }

        $film->update([
            'imdb_id' => $result['imdb_id'],
            'imdb_rating' => $result['imdb_rating'],
        ]);

        return $film;
    }

}
