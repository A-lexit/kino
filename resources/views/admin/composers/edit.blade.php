@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування композитора" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.composers.update', ['composer' => $composer->id])"
                            http-method="PUT"
                            field="name"
                            label="Ім'я"
                            :value="$composer->name"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
