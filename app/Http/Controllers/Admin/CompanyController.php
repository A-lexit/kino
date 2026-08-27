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
        $this->authorize('viewAny', Company::class);

        $companies = Company::latest('id')->paginate(20);

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $this->authorize('create', Company::class);

        return view('admin.companies.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Company::class);

        Company::create($request->validated());

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Компанію додано');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);

        // Viewer може відкрити форму для перегляду.
        $this->authorize('view', $company);

        return view('admin.companies.edit', compact('company'));
    }

    public function update(TitleRequest $request, $id)
    {
        $company = Company::findOrFail($id);

        // Admin та Editor можуть редагувати.
        $this->authorize('update', $company);

        $company->update($request->validated());

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:companies,id',
            'action' => 'required|string|in:delete',
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
