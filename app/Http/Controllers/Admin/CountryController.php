<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Country::class);

        $countries = Country::latest('id')->paginate(20);

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        $this->authorize('create', Country::class);

        return view('admin.countries.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Country::class);

        Country::create($request->validated());

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Країну додано');
    }

    public function edit(string $id)
    {
        $country = Country::findOrFail($id);

        // Viewer може відкрити форму для перегляду.
        $this->authorize('view', $country);

        return view('admin.countries.edit', compact('country'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $country = Country::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $country);

        $country->update($request->validated());

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Country $country)
    {
        $this->authorize('delete', $country);

        if ($country->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити країну «{$country->title}», оскільки вона пов'язана з фільмами!"
            ], 422);
        }

        $country->delete();

        return response()->json([
            'success' => true,
            'message' => 'Країну успішно видалено.'
        ]);
    }

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:countries,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $country = Country::find($id);

            if ($country && $country->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Країна «{$country->title}» пов'язана з фільмами."
                ], 422);
            }
        }

        Country::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані країни успішно видалено.'
        ]);
    }
}
