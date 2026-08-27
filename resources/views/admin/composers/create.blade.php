@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Додавання нового композитора" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.composers.store')"
                            field="name"
                            label="Ім'я"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

