@extends('admin.layouts.layout')
@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Добавить подписчика
                <small>приятные слова..</small>
            </h1>
        </section>

        <section class="content">
            <form role="form" method="post" action="{{ route('admin.subscribers.store') }}">
                @csrf
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Добавляем подписчика</h3>
                    @include('admin.layouts.alerts')
                </div>
                <div class="box-body">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="" name="email" value="{{old('email')}}">
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-success pull-right">Добавить</button>
                </div>
            </div>
            </form>

        </section>
    </div>

@endsection
