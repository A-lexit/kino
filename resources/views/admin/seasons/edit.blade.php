@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування сезону" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.seasons.update', ['season' => $season->id])"
                            http-method="PUT"
                            :value="$season->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
