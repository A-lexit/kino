@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагувати вікове обмеження" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.ages.update', ['age' => $age->id])"
                            http-method="PUT"
                            :value="$age->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
