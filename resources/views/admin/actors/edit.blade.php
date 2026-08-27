@extends('admin.layouts.layout')

@section('content')

    <x-admin.content-header title="Редагування топ-актора" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.actors.update', ['actor' => $actor->id])"
                            http-method="PUT"
                            field="name"
                            label="Ім'я"
                            :value="$actor->name"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
