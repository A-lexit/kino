@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування категорії" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.categories.update', ['category' => $category->id])"
                            http-method="PUT"
                            :value="$category->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
