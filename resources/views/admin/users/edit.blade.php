@extends('admin.layouts.layout')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Редагування користувача</h1>
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
                            <h3 class="card-title">Користувач "{{ $user->name }}"</h3>
                        </div>
                        <form role="form" method="post" action="{{ route('admin.users.update', ['user' => $user->id]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Ім'я користувача</label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror" id="name"
                                           value="{{ old('name', $user->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror" id="email"
                                           value="{{ old('email', $user->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Новий пароль</label>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror" id="password"
                                           placeholder="Залиш порожнім, щоб не змінювати">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="role">Роль</label>
                                    @if(auth()->id() == $user->id)
                                        <input type="text" class="form-control" value="{{ $user->role->label() }}" disabled>
                                        <small class="form-text text-muted">Свою власну роль змінити не можна (захист від самоблокування).</small>
                                    @else
                                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                            @foreach(\App\Enums\UserRole::cases() as $role)
                                                <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                                    {{ $role->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="mt-3">
                                        <img src=
                                                 {{ app(\App\Media\UserImageResolver::class)->image($user) }}
                                             alt="" width="150" class="img-thumbnail">
                                    </div>
                                    <label for="avatar" class="mt-2">Аватар</label>
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
