@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування якості відео" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.qualities.update', ['quality' => $quality->id])"
                            http-method="PUT"
                            :value="$quality->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
