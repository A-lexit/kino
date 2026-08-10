<!DOCTYPE html>
<html lang="uk" data-bs-theme="light">

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
    <link rel="icon" type="image/webp" sizes="16x16" href="{{ $images->favicon16($settings) }}">
    <link rel="icon" type="image/webp" sizes="32x32" href="{{ $images->favicon32($settings) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $images->appleTouchIcon($settings) }}">

    {{-- Bootstrap 5.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- Fancybox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css">

    {{-- Owl Carousel --}}
    <link rel="stylesheet" href="{{ asset('assets/front/css/owlcarousel2/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/owlcarousel2/owl.theme.default.min.css') }}">

    {{-- Основні стилі --}}
    <link rel="stylesheet" href="{{ asset('assets/front/css/style.css') }}">




        <script>
            (function() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    document.documentElement.setAttribute('data-bs-theme', savedTheme);
                } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                }
            })();
        </script>




    {{--<style>
        /* Перемикач теми */
        .theme-toggle {
            transition: transform 0.2s ease, color 0.2s ease;
            cursor: pointer;
        }

        .theme-toggle:hover {
            transform: rotate(15deg);
        }

        /* Зображення постерів */
        .film-poster img,
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Оптимізація темної теми для кастомних елементів */
        [data-bs-theme="dark"] .film-views,
        [data-bs-theme="dark"] .views-count {
            color: var(--bs-body-color-subtle) !important;
        }

        /* Сітка з автоматичним перенесенням на всіх екранах */
        .container-default {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 20px;
            width: 100%;
        }

        /* Контейнер картки */
        .child {
            width: 100%;
            max-width: 195px;
            justify-self: center; /* Центрує картку у своїй комірці grid */
        }

        /* Картинка всередині */
        .child img {
            width: 100%;
            height: auto;
            aspect-ratio: 194 / 293; /* Зберігає пропорції постера без стискання */
            object-fit: cover;
            vertical-align: bottom;
            border-radius: var(--radius-sm);
        }
    </style>--}}

    {{-- Vite --}}
    @vite('resources/js/app.js')

    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100 bg-body text-body">

{{-- Header --}}
@include('layouts.inc.header')

{{-- Flash messages --}}
<div class="container mt-3">
    @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

{{-- Main --}}
<main class="my-4 flex-grow-1">
    <div class="content-wrapper">
        @yield('content')
    </div>
</main>

{{-- Footer --}}
<footer class="footer mt-auto py-4 bg-body-tertiary">
    <div class="container">
        <div class="row align-items-center gy-3">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="footer-logo fw-bold fs-5">
                    <a href="{{ route('home') }}" class="text-decoration-none text-body">
                        {{ $settings->title ?? 'Kino' }}
                    </a>
                </div>
                <div class="footer-copy text-body-secondary small">
                    © {{ date('Y') }} Всі права захищені.
                </div>
            </div>

            <div class="col-lg-6 text-center text-lg-end">
                <div class="footer-description text-body-secondary small">
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeButtons = document.querySelectorAll('.theme-toggle');

        const syncIcons = (theme) => {
            themeButtons.forEach(btn => {
                const icon = btn.querySelector('i');
                if (!icon) return;

                if (theme === 'dark') {
                    // Жовте сонце підходить і під чорний, і під білий фон
                    icon.classList.remove('bi-moon-fill');
                    icon.classList.add('bi-sun-fill', 'text-warning');
                } else {
                    // Місяць успадковує власний колір кнопки
                    icon.classList.remove('bi-sun-fill', 'text-warning');
                    icon.classList.add('bi-moon-fill');
                }
            });
        };

        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            syncIcons(theme);
        };

        // 1. При завантаженні сторінки зчитуємо збережену тему з localStorage (або системну)
        const savedTheme = localStorage.getItem('theme')
            || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        // 2. Одразу застосовуємо збережену тему та синхронізуємо іконки
        setTheme(savedTheme);

        // 3. Обробка кліку по кнопках
        themeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });
        });
    });
</script>

@stack('scripts')

</body>

</html>
