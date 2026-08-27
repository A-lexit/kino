<?php
namespace App\Livewire\Admin;

use App\APIs\OmdbService;
use App\Models\Film;
use Livewire\Component;

class ImdbRatingFetcher extends Component
{
    public Film $film;

    public ?string $errorMessage = null;

    public function mount(Film $film): void
    {
        $this->film = $film;
    }

    public function fetch(OmdbService $omdbService): void
    {
        $this->errorMessage = null;

        $result = $omdbService->fetchRating($this->film);

        if (is_null($result)) {
            $this->errorMessage = 'Не вдалося знайти фільм на OMDb';
            return;
        }

        $this->film->update([
            'imdb_id' => $result['imdb_id'],
            'imdb_rating' => $result['imdb_rating'],
        ]);
    }

    public function render()
    {
        return view('livewire.admin.imdb-rating-fetcher');
    }

}
