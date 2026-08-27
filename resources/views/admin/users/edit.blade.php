@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Редагування користувача</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">

                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>

                        <li class="breadcrumb-item active">
                            Редагування користувача
                        </li>

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
                            <h3 class="card-title">
                                Користувач "{{ $user->name }}"
                            </h3>
                        </div>


                        <form role="form"
                              method="post"
                              action="{{ route('admin.users.update', ['user' => $user->id]) }}"
                              enctype="multipart/form-data">

                            @csrf
                            @method('PUT')


                            <div class="card-body">

                                {{-- Ім'я --}}
                                <div class="form-group">

                                    <label for="name">
                                        Ім'я користувача
                                    </label>

                                    <input type="text"
                                           name="name"
                                           id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}">

                                    @error('name')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                    @enderror

                                </div>


                                {{-- Email --}}
                                <div class="form-group">

                                    <label for="email">
                                        Email
                                    </label>

                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}">

                                    @error('email')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                    @enderror

                                </div>


                                {{-- Пароль --}}
                                <div class="form-group">

                                    <label for="password">
                                        Новий пароль
                                    </label>

                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Залиш порожнім, щоб не змінювати">

                                    @error('password')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                    @enderror

                                </div>


                                {{-- Роль --}}
                                <div class="form-group">

                                    <label for="role">
                                        Роль
                                    </label>

                                    @if(auth()->id() == $user->id)

                                        <input type="text"
                                               class="form-control"
                                               value="{{ $user->role->label() }}"
                                               disabled>

                                        <small class="form-text text-muted">
                                            Свою власну роль змінити не можна
                                            (захист від самоблокування).
                                        </small>

                                    @else

                                        <select name="role"
                                                id="role"
                                                class="form-control @error('role') is-invalid @enderror">

                                            @foreach(\App\Enums\UserRole::cases() as $role)

                                                <option value="{{ $role->value }}"
                                                    {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                                    {{ $role->label() }}
                                                </option>

                                            @endforeach

                                        </select>

                                        @error('role')

                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>

                                        @enderror

                                    @endif

                                </div>


                                {{-- Аватар --}}
                                <div class="form-group">

                                    <div class="mt-3">

                                        <img src="{{ app(\App\Media\UserImageResolver::class)->image($user) }}"
                                             alt=""
                                             width="150"
                                             class="img-thumbnail">

                                    </div>


                                    <label for="avatar"
                                           class="mt-2">
                                        Аватар
                                    </label>

                                    <input type="file"
                                           name="avatar"
                                           id="avatar"
                                           class="form-control">

                                </div>

                            </div>


                            <div class="card-footer">

                                @if(auth()->user()?->isAdmin())

                                    <button type="submit"
                                            class="btn btn-success">
                                        Зберегти
                                    </button>

                                @elseif(auth()->user()?->isViewer())

                                    <button type="button"
                                            class="btn btn-success opacity-50 cursor-not-allowed"
                                            disabled
                                            title="Редагування користувачів доступне лише адміністратору">
                                        Зберегти
                                    </button>

                                @endif

                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>

    </section>

@endsection
