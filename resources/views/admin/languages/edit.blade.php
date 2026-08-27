@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування мови озвучки" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.languages.update', ['language' => $language->id])"
                            http-method="PUT"
                            :value="$language->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
