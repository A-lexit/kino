@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Створення нової кінокомпанії" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.companies.store')"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
