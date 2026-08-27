@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування добірки" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.selections.update', ['selection' => $selection->id])"
                            http-method="PUT"
                            :value="$selection->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
