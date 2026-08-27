@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування країни" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.countries.update', ['country' => $country->id])"
                            http-method="PUT"
                            :value="$country->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
