@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Додавання нової мови" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.languages.store')"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
