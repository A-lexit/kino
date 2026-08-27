<?php
namespace App\Http\Controllers\Admin;

use App\Excel\Exports\FilmsExport;
use App\Excel\Imports\FilmsImport;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilmRequest;
use App\Models\Film;
use App\Services\FilmService;
use App\Services\FormDataService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FilmController extends Controller
{
    public function __construct(
        protected FormDataService $formDataService,
        protected FilmService $filmService
    ) {
        /* Middleware auth можна повернути, якщо маршрути
         * ще не захищені окремим middleware.
         */
    }

    public function index()
    {
        $this->authorize('viewAny', Film::class);

        $data = $this->filmService->getFilmsForUser();

        return view('admin.films.index', $data);
    }

    public function create()
    {
        $this->authorize('create', Film::class);

        $formData = $this->formDataService->getFormData();

        if (empty($formData['categories'])) {
            return redirect()
                ->route('admin.categories.create')
                ->with('warning', 'Спочатку необхідно створити хоча б одну категорію!');
        }

        $allFilms = Film::latest('id')
            ->pluck('title', 'id');

        return view('admin.films.create', compact(
            'formData',
            'allFilms'
        ));
    }

    public function store(FilmRequest $request)
    {
        $this->authorize('create', Film::class);

        $this->filmService->createFilm($request);

        return redirect()
            ->route('admin.films.index')
            ->with('success', 'Фільм додано');
    }

    public function edit(string $id)
    {
        $film = Film::findOrFail($id);

        /*
         * ВАЖЛИВО:
         * тут саме update, а не view.
         *
         * Viewer може переглядати фільм,
         * але не повинен навіть заходити на сторінку редагування.
         */
        $this->authorize('view', $film);

        $formData = $this->formDataService->getFormData();

        $allFilms = Film::where('id', '!=', $film->id)
            ->latest('id')
            ->pluck('title', 'id');

        return view('admin.films.edit', compact(
            'film',
            'formData',
            'allFilms'
        ));
    }

    public function update(FilmRequest $request, $id)
    {
        $film = Film::findOrFail($id);

        $this->authorize('update', $film);

        $this->filmService->updateFilm($film, $request);

        return redirect()
            ->route('admin.films.index')
            ->with('success', 'Фільм оновлено');
    }

    public function destroy($id)
    {
        $film = Film::withTrashed()->findOrFail($id);

        $this->authorize('delete', $film);

        $this->filmService->deleteFilm($film);

        return redirect()
            ->route('admin.films.index')
            ->with('success', 'Фільм видалено');
    }

    public function restore($id)
    {
        $film = Film::withTrashed()->findOrFail($id);

        $this->authorize('restore', $film);

        $this->filmService->restoreFilm($film);

        return redirect()
            ->route('admin.films.index')
            ->with('success', 'Фільм відновлено');
    }

    public function forceDelete($id)
    {
        $film = Film::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $film);

        $this->filmService->forceDeleteFilm($film);

        return redirect()
            ->route('admin.films.index')
            ->with('success', 'Фільм повністю видалено');
    }

    public function restoreAll(Request $request)
    {
        /*
         * Відновлення — тільки Admin.
         */
        abort_unless(
            $request->user()?->isAdmin(),
            403
        );

        $restored = $this->filmService->restoreAllFilms();

        return $this->respondWithFreshTables(
            $request,
            "Відновлено фільмів: {$restored}"
        );
    }

    public function forceDeleteAll(Request $request)
    {
        /*
         * Остаточне видалення — тільки Admin.
         */
        abort_unless(
            $request->user()?->isAdmin(),
            403
        );

        $count = $this->filmService->forceDeleteAllFilms();

        return $this->respondWithFreshTables(
            $request,
            "Видалено назавжди фільмів: {$count}"
        );
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Не вибрано жодного елемента.',
            ], 400);
        }

        $films = Film::withTrashed()
            ->whereIn('id', $ids)
            ->get();

        if ($films->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Фільми не знайдено.',
            ], 404);
        }

        /*
         * Усі bulk-операції зі статусом фільму
         * зараз доступні тільки Admin.
         *
         * Policy все одно перевіряється для кожного фільму.
         */
        foreach ($films as $film) {
            match ($action) {
                'delete' => $this->authorize('delete', $film),

                'restore' => $this->authorize('restore', $film),

                'force-delete' => $this->authorize('forceDelete', $film),

                default => abort(400, 'Невідома дія.'),
            };
        }

        switch ($action) {
            case 'delete':
                $count = $this->filmService->bulkDelete($films);
                $message = "Вибрані фільми переміщено в кошик: {$count}.";
                break;

            case 'restore':
                $count = $this->filmService->bulkRestore($films);
                $message = "Вибрані фільми успішно відновлено: {$count}.";
                break;

            case 'force-delete':
                $count = $this->filmService->bulkForceDelete($films);
                $message = "Вибрані фільми остаточно видалено: {$count}.";
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Невідома дія.',
                ], 400);
        }

        return $this->respondWithFreshTables(
            $request,
            $message
        );
    }

    protected function respondWithFreshTables(
        Request $request,
        string $message
    ): \Illuminate\Http\JsonResponse {
        $data = $this->filmService->getFilmsForUser();

        $activeHtml = view(
            'admin.films.partials.active-table',
            ['films' => $data['films']]
        )->render();

        $trashHtml = view(
            'admin.films.partials.trash-table',
            ['sdelfilms' => $data['sdelfilms']]
        )->render();

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
            return redirect()
                ->back()
                ->with('error', 'Не вдалося знайти фільм на OMDb');
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Рейтинг IMDB оновлено: ' . $result->imdb_rating
            );
    }

    public function export()
    {
        abort_if(
            !auth()->user()?->isAdmin(),
            403,
            'Експорт доступний лише адміністраторам.'
        );

        return Excel::download(
            new FilmsExport,
            'films-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function importStore(Request $request)
    {
        abort_if(
            !auth()->user()?->isAdmin(),
            403,
            'Імпорт доступний лише адміністраторам.'
        );

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $mode = $request->input('import_mode', 'soft');

        $import = new FilmsImport($mode);

        Excel::import($import, $request->file('file'));

        return redirect()
            ->back()
            ->with('import_result', [
                'message' => 'Імпорт файлу завершено.',
                'successCount' => $import->successCount,
                'updatedCount' => $import->updatedCount,
                'skippedCount' => $import->skippedCount,
                'failCount' => $import->failCount,
                'warnings' => $import->warnings,
            ]);
    }
}
