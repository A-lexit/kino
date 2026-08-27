@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Додавання країни" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.countries.store')"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
