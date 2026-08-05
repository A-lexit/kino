<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $settings->title ?? 'Kino')</title>
    <meta name="description" content="@yield('description', $settings->description ?? '')">

    <meta property="og:locale" content="uk_UA">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', $settings->title ?? 'Kino')">
    <meta property="og:description" content="@yield('description', $settings->description ?? '')">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <meta name="twitter:card" content="summary_large_image">

    @php($images = app(\App\Media\SettingsImageResolver::class))

    {{-- Favicons --}}
    <link rel="icon"
          type="image/webp"
          sizes="16x16"
          href="{{ $images->favicon16($settings) }}">

    <link rel="icon"
          type="image/webp"
          sizes="32x32"
          href="{{ $images->favicon32($settings) }}">

    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ $images->appleTouchIcon($settings) }}">


    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
          rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- Fancybox --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css">

    {{-- Owl Carousel --}}
    <link rel="stylesheet"
          href="{{ asset('assets/front/css/owlcarousel2/owl.carousel.min.css') }}">

    <link rel="stylesheet"
          href="{{ asset('assets/front/css/owlcarousel2/owl.theme.default.min.css') }}">


    {{-- Основні стилі --}}
    <link rel="stylesheet"
          href="{{ asset('assets/front/css/style.css') }}">


    {{-- Vite --}}
    @vite('resources/js/app.js')

    @stack('styles')

</head>

<body>

{{-- Header --}}
@include('layouts.inc.header')

{{-- Flash messages --}}
<div class="container mt-3">

    @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('status') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

</div>

{{-- Main --}}
<main>

    <div class="content-wrapper">

        @yield('content')

    </div>

</main>

{{-- Footer --}}
<footer class="py-4 mt-auto">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">

                © {{ date('Y') }}
                <a href="{{ route('home') }}">
                    {{ $settings->title ?? 'Kino' }}
                </a>

            </div>

            <div class="col-md-6 text-center text-md-end">

                <small class="text-secondary">
                    Онлайн-каталог фільмів та серіалів
                </small>

            </div>

        </div>

    </div>

</footer>

{{-- Fancybox --}}
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>

{{-- Owl --}}
<script src="{{ asset('assets/front/js/owlcarousel2/jquery-3.6.1.min.js') }}"></script>

<script src="{{ asset('assets/front/js/owlcarousel2/owl.carousel.min.js') }}"></script>

<script src="{{ asset('assets/front/js/owlcarousel2/main.js') }}"></script>

@stack('scripts')

</body>
</html>
