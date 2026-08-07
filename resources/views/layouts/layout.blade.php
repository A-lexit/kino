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

    <style>

        .child p {
            font-size: 13px;
            font-weight: 200;
            max-width: 195px;
            overflow: hidden;
            text-overflow: ellipsis;
        }


        .child-infilm p {
            font-size: 11px;
            width: 150px;
        }

        .pagination-new {
            width: 100%;
            margin-top: 40px;
        }

        .film-gallery-grid {
            margin-top: 8px;
            margin-bottom: 0px !important;
        }


        .blog-title-area {
            margin-top: 7px;
        }

        .sidetitle h3 {
            text-align: left;
            font-weight: 600;
        }

        .watchmore h3 {
            text-align: left;
        }

        .child-archive img {
            max-width: 155px;
            overflow: hidden;
            text-overflow: ellipsis;
        }




        .footer{
            background:#212529;
            border-top:3px solid #5b5e75;

        }

        .footer-logo a{
            color:#fff;
            font-size:22px;
            font-weight:700;
            text-decoration:none;
            transition:.2s;
        }

        .footer-logo a:hover{
            color:#8ea0ff;
        }

        .footer-copy{
            color:#9ca3af;
            font-size:14px;
            margin-top:6px;
        }

        .footer-description{
            color:#bfc3cf;
            font-size:14px;
            line-height:1.5;
        }






        .pagination-new{
            overflow-x:auto;
            white-space:nowrap;
            text-align:center;
        }

        .pagination{
            flex-wrap:nowrap;
            width:max-content;
            margin:auto;
        }




        .archive-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:14px;
            margin-top:20px;
        }

        .archive-item{
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:54px;
            padding:12px 16px;
            background:#fff;
            border:1px solid #e4e7eb;
            border-radius:10px;
            color:#313131;
            font-weight:500;
            text-align:center;
            transition:.18s ease;
        }

        .archive-item:hover{
            background:#f8f9fa;
            border-color:#5b5e75;
            color:#5b5e75;
        }

        @media (max-width:991px){
            .archive-grid{
                grid-template-columns:repeat(2,1fr);
            }
        }

        @media (max-width:580px){
            .archive-grid{
                grid-template-columns:1fr;
            }
        }

    </style>




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
<footer class="footer mt-auto py-4">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6 text-center text-lg-start mb-3 mb-lg-0">

                <div class="footer-logo">
                    <a href="{{ route('home') }}">
                        {{ $settings->title ?? 'Kino' }}
                    </a>
                </div>

                <div class="footer-copy">
                    © {{ date('Y') }} Всі права захищені.
                </div>

            </div>

            <div class="col-lg-6 text-center text-lg-end">

                <div class="footer-description">
                    Онлайн-каталог фільмів, серіалів, мультфільмів та мультсеріалів.
                </div>

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
