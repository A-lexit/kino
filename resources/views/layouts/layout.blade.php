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


               /* =========================================================
                  FILM SHOW PAGE
                  ========================================================= */

               /* ---------- Page layout ---------- */

           .film-page {
               display: flex;
               align-items: flex-start;
               gap: 30px;
               margin-bottom: 100px;
           }

        /* ---------- Desktop sidebar ---------- */

        .film-sidebar-desktop {
            width: 27%;
            flex: 0 0 27%;
            min-width: 0;
        }

        /* ---------- Main content ---------- */

        .film-main {
            flex: 1;
            min-width: 0;

            display: block;

            background-color: #f0f0f0;
            padding: 20px;

            font-size: 15px;

            border-radius: 20px;
        }

        /* ---------- Header ---------- */

        .film-header {
            text-align: left;
            background: transparent;
            margin: 0 0 20px;
            padding: 0;

            padding-bottom: 6px;
            border-bottom: 2px solid #e1e7e9;
        }

        .film-header h1 {
            margin: 0;
            text-align: left;
        }

        .film-origin-title {
            margin-top: 8px;
            text-align: left;
            font-size: 1.1rem;
        }

        /* ---------- Mobile blocks ---------- */

        .film-mobile-block {
            display: none;
        }

        /* ---------- Mobile poster ---------- */

        .film-poster {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 12px;
        }

        .film-release-date {
            margin: 16px 0 0;
        }

        /* ---------- Mobile information ---------- */

        .film-mobile-info {
            margin-top: 25px;
        }

        .film-mobile-info h2,
        .film-mobile-sidebar h2 {
            margin: 0 0 15px;
            font-size: 22px;
        }

        .film-info-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .film-info-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;

            line-height: 1.5;
        }

        .film-info-row > span:first-child {
            flex: 0 0 auto;
            font-weight: 600;
        }

        .film-info-row > div {
            min-width: 0;
        }

        .film-info-note {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #d7d7d7;
        }

        .film-info-note strong {
            display: block;
            margin-bottom: 5px;
        }

        /* ---------- Main film details ---------- */

        .film-details {
            margin-top: 25px;
        }

        .film-detail-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;

            padding: 7px 0;

            line-height: 1.5;
        }

        .film-detail-row > span:first-child {
            flex: 0 0 150px;
            /*font-weight: 600;*/
        }

        .film-detail-row > div {
            min-width: 0;
        }

        /* ---------- Sections ---------- */

        .film-section {
            margin-top: 40px;
        }

        .film-section h2 {
            margin: 0 0 15px;
            font-size: 24px;
            line-height: 1.3;
        }

        /* ---------- Empty blocks ---------- */

        .film-empty-block {
            padding: 15px 18px;

            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;

            color: #666;
        }

        /* ---------- Description ---------- */

        .film-description {
            line-height: 1.7;
        }

        .film-description p:last-child {
            margin-bottom: 0;
        }

        /* ---------- Related films ---------- */

        .related-films {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px;
        }

        .related-film {
            min-width: 0;
        }

        .related-film img {
            display: block;

            width: 100%;
            height: auto;


        }

        .related-film a {
            display: block;

            margin-top: 8px;

            color: #313131;
            font-weight: 500;
            text-decoration: none;

            line-height: 1.4;
        }

        .related-film a:hover {
            color: #5b5e75;
        }

        /* ---------- Mobile sidebar content ---------- */

        .film-mobile-sidebar {
            margin-top: 40px;
        }

        .sidebar-list {
            margin: 0 0 30px;
            padding: 0;

            list-style: none;
        }

        .sidebar-list li {
            padding: 8px 0;

            border-bottom: 1px solid #d7d7d7;
        }

        .sidebar-list li:first-child {
            border-top: 1px solid #d7d7d7;
        }

        .sidebar-list a {
            color: #313131;
            text-decoration: none;
        }

        .sidebar-list a:hover {
            color: #5b5e75;
        }

        /* ---------- Upcoming movies ---------- */

        .upcoming-movie {
            padding: 10px 0;

            border-bottom: 1px solid #d7d7d7;
        }

        .upcoming-movie a {
            display: block;

            color: #313131;
            font-weight: 600;
            text-decoration: none;
        }

        .upcoming-movie a:hover {
            color: #5b5e75;
        }

        .upcoming-movie small {
            display: block;

            margin-top: 4px;

            color: #777;
        }

        /* ---------- Comments ---------- */

        .film-comments {
            margin-top: 40px;
        }



        /* =========================================================
           TABLET
           ========================================================= */

        @media (max-width: 991px) {

            .film-page {
                gap: 20px;
            }

            .film-sidebar-desktop {
                width: 30%;
                flex-basis: 30%;
            }

            .film-main {
                padding: 18px;
            }

            .film-header h1 {
                font-size: 28px;
            }

            .related-films {
                grid-template-columns: repeat(3, 1fr);
            }

        }


        /* =========================================================
           MOBILE
           ========================================================= */

        @media (max-width: 768px) {

            /* Main layout becomes one column */

            .film-page {
                display: block;

                /*width: 100%;*/

                margin-bottom: 50px;
            }

            /* Hide original desktop sidebar */

            .film-sidebar-desktop {
                display: none;
            }

            /* Main block occupies full width */

            .film-main {
                width: 100%;

                padding: 15px;

                border-radius: 15px;

                font-size: 15px;
            }

            /* Header */

            .film-header {
                margin-bottom: 20px;
            }

            .film-header h1 {
                font-size: 26px;
                line-height: 1.25;
            }

            .film-origin-title {
                font-size: 16px;
            }

            /* Show mobile sidebar blocks */

            .film-mobile-block {
                display: block;
            }

            /* Poster */

            .film-mobile-poster {
                margin-top: 20px;
            }

            .film-poster {
                width: 100%;
                max-width: 100%;
                height: auto;
            }

            /* Info */

            /* Info */

            .film-mobile-info {
                margin-top: 25px;
            }

            .film-info-row {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                padding: 2px 0;
                line-height: 1.5;
            }

            .film-info-row > span:first-child {
                flex: 0 0 150px;
                font-weight: 600;
                /*display: inline-block;
                flex: 0 0 auto;
                margin-right: 0;*/
            }

            .film-info-row > div {
                min-width: 0;
            }

            /* Main details */

            .film-details {
                margin-top: 30px;
            }

            .film-detail-row {
                display: flex;

                padding: 7px 0;
            }

            .film-detail-row > span:first-child {
                display: inline;

                margin-right: 5px;
            }

            /* Sections */

            .film-section {
                margin-top: 35px;
            }

            .film-section h2 {
                font-size: 22px;
            }

            /* Related films */

            .related-films {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            /* Mobile sidebar */

            .film-mobile-sidebar {
                margin-top: 40px;
            }

            /* Comments */

            .film-comments {
                margin-top: 35px;
            }



            .film-info-note {
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }

            .film-info-note > span {
                flex: 0 0 150px;
            }

            .film-info-note > div {
                flex: 1;
                min-width: 0;
            }








        }


        /* =========================================================
           SMALL MOBILE
           ========================================================= */

        @media (max-width: 480px) {

            .film-main {
                padding: 12px;
                border-radius: 12px;
            }

            .film-header h1 {
                font-size: 23px;
            }

            .film-origin-title {
                font-size: 15px;
            }

            .film-section h2,
            .film-mobile-info h2,
            .film-mobile-sidebar h2 {
                font-size: 20px;
            }

            .related-films {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

        }




        /* INDEX — більший */
        .archive-area .first-col-film span {
            display: inline-block;
            font-size: 19px !important;
            line-height: 1.2 !important;
            font-weight: 500;
        }

        /* SHOW — трохи менший */
        .film-detail-label {
            display: inline-block;
            font-size: 15px;
            line-height: 1.3 !important;
            font-weight: 500;
            position: relative;
            bottom: 1.5px;
        }

        .span-show {
            font-size: 13px !important;
        }

        .span-index {
            font-size: 14.6px !important;
        }



















        @media (min-width: 769px) {
            .section-default-posts {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            .container-default {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 20px;
                width: 100%;
            }

            .child {
                width: auto;
                min-width: 0;
            }

            .child img {
                width: 100%;
                max-width: 195px;
                height: auto;
            }

            .child p {
                max-width: 195px;
                text-align: center;
            }
        }


        @media (max-width: 768px) {
            .section-default-posts {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            .container-default {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
                width: 100%;
            }

            .child {
                width: auto;
                min-width: 0;
            }

            .child img {
                width: 100%;
                max-width: 195px;
                height: auto;
            }

            .child p {
                max-width: 195px;
                text-align: center;
            }
        }







        /*Обрізання довгих назв*/
        .film-title-text {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }
        .related-category-title {
            display: inline-block;
            max-width: 70%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: bottom;
        }
        .related-film-title {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
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
