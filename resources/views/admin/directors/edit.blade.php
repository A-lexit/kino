@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування режисера" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.directors.update', ['director' => $director->id])"
                            http-method="PUT"
                            field="name"
                            label="Ім'я"
                            :value="$director->name"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
