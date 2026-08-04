<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilmRequest;
use App\Services\FormDataService;
use App\Services\FilmService;
use Illuminate\Http\Request;
use App\Models\Film;

class FilmController extends Controller
{
    public function __construct(
        protected FormDataService $formDataService,
        protected FilmService $filmService
    ) {
        $this->middleware('auth')->except('index');
    }

    public function index()
    {
        $this->authorize('viewAny', Film::class);

        $user = auth()->user();
        $data = $this->filmService->getFilmsForUser($user);

        return view('admin.films.index', $data);
    }

    public function create()
    {
        $this->authorize('create', Film::class);

        $formData = $this->formDataService->getFormData();
        return view('admin.films.create', compact('formData'));
    }

    public function store(FilmRequest $request)
    {
        $this->authorize('create', Film::class);

        $this->filmService->createFilm($request);
        return redirect()->route('admin.films.index')->with('success', 'Фільм додано');
    }


    public function edit(string $id)
    {
        $film = $this->filmService->findFilm($id);
        $this->authorize('view', $film); // <-- було 'update'

        $formData = $this->formDataService->getFormData();
        return view('admin.films.edit', compact('film', 'formData'));
    }


    public function update(FilmRequest $request, $id)
    {
        $film = $this->filmService->findFilm($id);
        $this->authorize('update', $film);

        $film = $this->filmService->updateFilm($id, $request);

        if (is_null($film)) {
            return redirect()->route('admin.films.index')->with('error', 'Фільм не знайдено');
        }

        return redirect()->route('admin.films.index')->with('success', 'Фільм оновлено');
    }

    public function destroy($id)
    {
        $film = Film::withTrashed()->findOrFail($id);
        $this->authorize('delete', $film);

        $film = $this->filmService->deleteFilm($id);

        if (is_null($film)) {
            return redirect()->route('admin.films.index')->with('error', 'Фільм не знайдено');
        }

        return redirect()->route('admin.films.index')->with('success', 'Фільм видалено');
    }

    public function restore($id)
    {
        $film = Film::withTrashed()->findOrFail($id);
        $this->authorize('restore', $film);

        $film = $this->filmService->restoreFilm($id);

        if (is_null($film)) {
            return redirect()->route('admin.films.index')->with('error', 'Фільм не знайдено або не видалено');
        }

        return redirect()->route('admin.films.index')->with('success', 'Фільм відновлено');
    }

    public function forceDelete($id)
    {
        $film = Film::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $film);

        $film = $this->filmService->forceDeleteFilm($id);

        if (is_null($film)) {
            return redirect()->route('admin.films.index')->with('error', 'Фільм не знайдено');
        }

        return redirect()->route('admin.films.index')->with('success', 'Фільм повністю видалено');
    }

    public function restoreAll(Request $request)
    {
        // Відновлення ВСІХ фільмів у кошику разом — не прив'язане до конкретного
        // фільму, тому Policy тут не застосувати напряму; дозволяємо тільки Admin
        abort_unless($request->user()->isAdmin(), 403);

        $restored = $this->filmService->restoreAllFilms();

        return $this->respondWithFreshTables($request, "Відновлено фільмів: {$restored}");
    }

    public function forceDeleteAll(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $count = $this->filmService->forceDeleteAllFilms();

        return $this->respondWithFreshTables($request, "Видалено назавжди фільмів: {$count}");
    }

    public function bulkAction(Request $request)
    {
        // delete/restore/force-delete — за FilmPolicy усі три дії доступні
        // тільки Admin (Editor не видаляє навіть свої фільми), тому єдина
        // перевірка на вході достатня, без Policy на кожен окремий фільм
        abort_unless($request->user()->isAdmin(), 403);

        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Не вибрано жодного елемента.'], 400);
        }

        $query = Film::withTrashed()->whereIn('id', $ids);

        switch ($action) {
            case 'delete':
                $query->delete();
                $message = 'Вибрані фільми переміщено в кошик.';
                break;
            case 'restore':
                $query->restore();
                $message = 'Вибрані фільми успішно відновлено.';
                break;
            case 'force-delete':
                $query->forceDelete();
                $message = 'Вибрані фільми остаточно видалено.';
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Невідома дія.'], 400);
        }

        return $this->respondWithFreshTables($request, $message);
    }

    protected function respondWithFreshTables(Request $request, string $message): \Illuminate\Http\JsonResponse
    {
        $data = $this->filmService->getFilmsForUser(auth()->user());

        $activeHtml = view('admin.films.partials.active-table', ['films' => $data['films']])->render();
        $trashHtml = view('admin.films.partials.trash-table', ['sdelfilms' => $data['sdelfilms']])->render();

        return response()->json([
            'success' => true,
            'message' => $message,
            'activeHtml' => $activeHtml,
            'trashHtml' => $trashHtml,
        ]);
    }

    public function fetchImdbRating(Film $film)
    {
        $this->authorize('update', $film);

        $result = $this->filmService->fetchImdbRating($film);

        if (is_null($result)) {
            return redirect()->back()->with('error', 'Не вдалося знайти фільм на OMDb');
        }

        return redirect()->back()->with('success', 'Рейтинг IMDB оновлено: ' . $result->imdb_rating);
    }
}
