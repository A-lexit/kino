@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування продюсера" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.producers.update', ['producer' => $producer->id])"
                            http-method="PUT"
                            field="name"
                            label="Ім'я"
                            :value="$producer->name"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
