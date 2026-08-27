@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування рейтингу" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.ratings.update', ['rating' => $rating->id])"
                            http-method="PUT"
                            :value="$rating->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
