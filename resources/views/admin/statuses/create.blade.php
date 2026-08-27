@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Створення статусу серіалу/мультсеріалу" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.statuses.store')"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
