<?php

namespace App\Http\Controllers;

use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::paginate(20);
        return view('companies.index', compact('companies'));
    }

    public function show($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $films = $company->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('companies.show', compact('company', 'films'));
    }

}
