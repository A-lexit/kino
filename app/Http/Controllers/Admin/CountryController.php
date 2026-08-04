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
        $countries = Country::paginate(20);
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(TitleRequest $request)
    {
        Country::create($request->all());
        return redirect()->route('admin.countries.index')->with('success', 'Країну додано');
    }

    public function edit(string $id)
    {
        $country = Country::findOrFail($id);
        return view('admin.countries.edit', compact('country'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $country = Country::findOrFail($id);
        $country->update($request->all());
        return redirect()->route('admin.countries.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Country $country)
    {
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

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:countries,id',
            'action' => 'required|string|in:delete'
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
