@extends('layouts.app')
@section('content')

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0 fw-bold">{{ __('Редагування профілю') }}</h5>
                    </div>

                    <div class="card-body p-4">
                        <!-- Аватар користувача (збільшений вдвічі) -->
                        <div class="text-center mb-4">
                            <img src=
                                     {{--"{{ $user->getImage() }}"--}}
                                     "{{ app(\App\Media\UserImageResolver::class)->image($user) }}"
                                 alt="User Avatar"
                                 class="rounded-circle img-thumbnail shadow-sm"
                                 style="width: 250px; height: 250px; object-fit: cover;">
                        </div>

                        <form role="form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Поле: Ім'я -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold text-secondary">Ім'я користувача</label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror" id="name"
                                       value="{{ $user->name }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Поле: Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold text-secondary">Email адреса</label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror" id="email"
                                       value="{{ $user->email }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Поле: Пароль -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold text-secondary">Новий пароль</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" id="password"
                                       placeholder="Залиште порожнім, якщо не змінюєте">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Поле: Файл аватара -->
                            <div class="mb-4">
                                <label for="image" class="form-label fw-bold text-secondary">Змінити аватар</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="image" name="avatar">
                                @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Кнопка відправки форми -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                                    Оновити профіль
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
