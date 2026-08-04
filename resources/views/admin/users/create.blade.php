@extends('admin.layouts.layout')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Новий користувач</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Blank Page</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Новий користувач</h3>
                        </div>
                        <form role="form" method="post" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Ім'я користувача</label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror" id="name"
                                           value="{{ old('name') }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror" id="email"
                                           value="{{ old('email') }}">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Пароль</label>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror" id="password">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="role">Роль</label>
                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                        @foreach(\App\Enums\UserRole::cases() as $role)
                                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                                {{ $role->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Admin — повний доступ. Editor — створює/редагує лише свої фільми, не видаляє.
                                        Viewer — тільки перегляд в адмінці. Користувач сайту — без доступу в адмінку.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="avatar">Аватар</label>
                                    <input type="file" name="avatar" id="avatar" class="form-control-file">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Зберегти</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
