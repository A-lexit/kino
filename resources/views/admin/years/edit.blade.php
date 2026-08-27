@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагувати рік випуску" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.years.update', ['year' => $year->id])"
                            http-method="PUT"
                            :value="$year->title"
                        />

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
