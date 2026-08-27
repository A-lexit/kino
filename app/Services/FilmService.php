<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Film;
use Illuminate\Http\Request;
use App\Http\Requests\FilmRequest;
use App\Media\FilmImageMedia;
use App\Media\FilmVideoResolver;

class FilmService
{
    public function __construct(
        protected FilmImageMedia $filmImageMedia,
        protected \App\Media\FilmVideoMedia $filmVideoMedia,
        protected \App\APIs\OmdbService $omdbService,
        protected FilmRelationService $relationService
    ) {}

    public function getFilmsForUser(): array
    {
        $with = [
            'category:id,title,slug',
            'actors:id,name',
        ];

        $films = Film::with($with)
            ->latest('id')
            ->get();

        $sdelfilms = Film::onlyTrashed()
            ->latest('id')
            ->get();

        return compact('films', 'sdelfilms');
    }

    public function createFilm(FilmRequest $request): Film
    {
        return DB::transaction(function () use ($request) {

            $data = $request->validated();

            $likes = $data['likes'] ?? 0;
            $views = $data['views'] ?? 0;

            unset(
                $data['likes'],
                $data['views'],
                $data['related_films']
            );

            if (is_null($data['sort_order'] ?? null)) {
                unset($data['sort_order']);
            }

            $this->filmImageMedia->uploadFilmImages(
                $request,
                $data,
                null,
                $data['slug'] ?? null
            );

            $this->filmVideoMedia->uploadTrailer(
                $request,
                $data
            );

            $this->handleTrailerYoutubeUrl($data);

            $film = Film::create($data);

            $this->syncFilmState(
                $film,
                $likes,
                $views
            );

            $this->afterSaveActions(
                $film,
                $request
            );

            return $film;
        });
    }

    public function updateFilm(
        Film $film,
        FilmRequest $request
    ): Film {
        return DB::transaction(function () use ($film, $request) {

            $data = $request->validated();

            /*
             * Slug може змінювати тільки Admin.
             *
             * Editor може редагувати фільм,
             * але slug залишається під контролем Admin.
             */
            if (auth()->user()?->isAdmin()) {

                if (empty($data['slug'])) {
                    $data['slug'] = $film->slug;
                } else {
                    $data['slug'] = Str::slug($data['slug']);
                }

            } else {
                unset($data['slug']);
            }

            $likes = array_key_exists('likes', $data)
                ? ($data['likes'] ?? 0)
                : ($film->state?->likes ?? 0);

            $views = array_key_exists('views', $data)
                ? ($data['views'] ?? 0)
                : ($film->state?->views ?? 0);

            unset(
                $data['likes'],
                $data['views'],
                $data['related_films']
            );


            $this->filmImageMedia->uploadFilmImages(
                $request,
                $data,
                $film,
                $data['slug'] ?? $film->slug
            );

            foreach ([
                         'thumbnail',
                         'gal_image1',
                         'gal_image2',
                         'gal_image3',
                         'gal_image4',
                         'gal_image5',
                     ] as $field) {
                if (
                    $request->boolean("delete_{$field}")
                    && !$request->hasFile($field)
                ) {
                    $this->filmImageMedia->deleteFilmImage(
                        $film,
                        $field
                    );

                    $data[$field] = null;
                }
            }


            $this->filmVideoMedia->uploadTrailer(
                $request,
                $data,
                $film
            );

            $this->handleTrailerYoutubeUrl($data);

            $film->fill($data);
            $film->save();

            $this->syncFilmState(
                $film,
                $likes,
                $views
            );

            $this->afterSaveActions(
                $film,
                $request
            );

            return $film;
        });
    }

    public function deleteFilm(Film $film): void
    {
        $film->delete();
    }

    public function restoreFilm(Film $film): void
    {
        if ($film->trashed()) {
            $this->ensureTitleExists($film);
            $film->restore();
        }
    }

    public function restoreAllFilms(): int
    {
        $count = 0;

        Film::onlyTrashed()
            ->chunkById(100, function ($films) use (&$count) {

                Film::withoutEvents(function () use (
                    $films,
                    &$count
                ) {
                    foreach ($films as $film) {
                        $this->ensureTitleExists($film);
                        $film->restore();
                        $count++;
                    }
                });
            });

        return $count;
    }

    public function forceDeleteFilm(Film $film): void
    {
        $this->filmImageMedia->deleteFilmImages($film);
        $this->filmVideoMedia->deleteTrailer($film);

        $film->forceDelete();
    }

    public function forceDeleteAllFilms(): int
    {
        $count = 0;

        Film::onlyTrashed()
            ->chunkById(100, function ($films) use (&$count) {

                Film::withoutEvents(function () use (
                    $films,
                    &$count
                ) {
                    foreach ($films as $film) {

                        $this->filmImageMedia
                            ->deleteFilmImages($film);

                        $this->filmVideoMedia
                            ->deleteTrailer($film);

                        $film->forceDelete();

                        $count++;
                    }
                });
            });

        return $count;
    }

    protected function ensureTitleExists(Film $film): void
    {
        if (empty($film->title)) {
            $film->title = 'Невідомий фільм ' . uniqid();
        }
    }

    protected function afterSaveActions(
        Film $film,
        Request $request
    ): void {
        if (is_null($film->category_id)) {
            $film->togglePublishStatus(null);
        } else {
            $film->togglePublishStatus(
                $request->get('publish_status')
            );
        }

        $film->toggleFeatured(
            $request->get('is_featured')
        );

        $this->relationService->sync(
            $film,
            $request->all()
        );
    }


    private function handleTrailerYoutubeUrl(
        array &$data
    ): void {
        if (empty($data['trailer_youtube_url'])) {
            unset($data['trailer_youtube_url']);
            return;
        }

        $id = FilmVideoResolver::extractYoutubeId(
            $data['trailer_youtube_url']
        );

        if ($id) {
            $data['trailer_youtube_id'] = $id;
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

    public function bulkDelete($films): int
    {
        $count = 0;

        foreach ($films as $film) {
            if ($film->trashed()) {
                continue;
            }

            $this->deleteFilm($film);
            $count++;
        }

        return $count;
    }

    public function bulkRestore($films): int
    {
        $count = 0;

        foreach ($films as $film) {
            if (!$film->trashed()) {
                continue;
            }

            $this->restoreFilm($film);
            $count++;
        }

        return $count;
    }

    public function bulkForceDelete($films): int
    {
        $count = 0;

        foreach ($films as $film) {
            $this->forceDeleteFilm($film);
            $count++;
        }

        return $count;
    }

    protected function syncFilmState(
        Film $film,
        int $likes,
        int $views
    ): void {
        $film->state()->updateOrCreate(
            ['film_id' => $film->id],
            [
                'likes' => $likes,
                'views' => $views,
            ]
        );
    }

}
