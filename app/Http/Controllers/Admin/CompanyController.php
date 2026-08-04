<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::paginate(20);
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(TitleRequest $request)
    {
        Company::create($request->all());
        return redirect()->route('admin.companies.index')->with('success', 'Компанію додано');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('admin.companies.edit', compact('company'));
    }

    public function update(TitleRequest $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->all());
        return redirect()->route('admin.companies.index')->with('success', 'Зміни збережені');
    }

    // Одиночне видалення через AJAX
    public function destroy(Company $company)
    {
        if ($company->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити компанію «{$company->title}», оскільки вона пов'язана з фільмами!"
            ], 422);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Компанію успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:companies,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $company = Company::find($id);
            if ($company && $company->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Компанія «{$company->title}» пов'язана з фільмами."
                ], 422);
            }
        }

        Company::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані компанії успішно видалено.'
        ]);
    }
}
