@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування кінокомпанії" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.companies.update', ['company' => $company->id])"
                            http-method="PUT"
                            :value="$company->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
