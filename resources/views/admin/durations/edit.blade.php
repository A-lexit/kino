@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Редагування тривалості перегляду (хв.)" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <x-admin.forms.simple-title-form
                            :action="route('admin.durations.update', ['duration' => $duration->id])"
                            http-method="PUT"
                            :value="$duration->title"
                        />

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
