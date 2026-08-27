<header>
    {{-- Верхня інформаційна панель --}}
    <div class="bg-black text-white small">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-2">
                {{-- Ліва частина: Дата та Курси валют --}}
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @if($currentDate)
                        <span class="text-white-50">
                            <i class="bi bi-calendar3 me-1 text-white"></i>
                            {{ $currentDate->format('d.m.Y') }}
                        </span>
                    @endif

                    @if($currency)
                        <span class="text-white-50 d-none d-sm-inline">
                            USD <strong class="text-white">{{ $currency['USD'] }}</strong>
                        </span>
                        <span class="text-white-50 d-none d-sm-inline">
                            EUR <strong class="text-white">{{ $currency['EUR'] }}</strong>
                        </span>
                    @endif
                </div>

                {{-- Права частина верхньої смуги --}}
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    @guest
                        <div class="d-none d-lg-flex gap-3">
                            <a class="text-white text-decoration-none" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                            <a class="text-white text-decoration-none" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </a>
                        </div>
                    @else
                        <div class="dropdown d-none d-lg-block">
                            <a href="#" class="dropdown-toggle text-white text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                @can('viewAny', \App\Models\Film::class)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i>Адмінка
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endcan

                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="bi bi-person me-2"></i>Профіль
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest

                    {{-- Елементи керування для Mobile --}}
                    <div class="d-flex align-items-center gap-1 d-lg-none ps-2 ms-1">
                        <button type="button"
                                class="btn btn-sm btn-outline-light border-0 theme-toggle d-flex align-items-center justify-content-center p-1"
                                aria-label="Перемкнути тему"
                                title="Перемкнути тему"
                                style="width: 32px; height: 32px; border-radius: 50%;">
                            <i class="bi bi-moon-fill fs-6"></i>
                        </button>

                        <button class="navbar-toggler p-1 text-white border-0"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#mainMenu"
                                aria-controls="mainMenu"
                                aria-label="Меню">
                            <i class="bi bi-list fs-3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Основна шапка --}}
    <div class="bg-body border-bottom-customm shadow-sm">

            <div class="container py-2 py-lg-3">
                <div class="row align-items-center gy-2 position-relative">

                    <div class="col-12 col-lg-2 text-center text-lg-start">
                        <a href="{{ route('home') }}" class="navbar-brand d-inline-block">
                            <img src="{{ app(\App\Media\SettingsImageResolver::class)->logo($settings) }}"
                                 alt="{{ $settings->title ?? 'Kino' }}"
                                 style="height: 60px; width: auto; max-width: 100%;">
                        </a>
                    </div>

                    <div class="col-12 col-lg-7 d-flex align-items-center">
                        <div id="search-app" class="mx-auto w-100" style="max-width: 650px;">
                            <search-component></search-component>
                        </div>
                    </div>

                    <div class="col-lg-3 d-none d-lg-flex justify-content-end align-items-center">
                        <button type="button"
                                class="btn btn-outline-secondary border-0 theme-toggle d-flex align-items-center justify-content-center"
                                aria-label="Перемкнути тему"
                                title="Перемкнути тему"
                                style="width: 40px; height: 40px; border-radius: 50%;">
                            <i class="bi bi-moon-fill fs-5"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <nav class="navbar navbar-expand-lg header-nav-bg-customm d-none d-lg-block py-1 pt-3 pb-3">
        <div class="container">
            <ul class="navbar-nav mx-auto gap-2 align-items-center">
                @foreach($menuItems as $item)
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->is(...$item['is_patterns']) ? 'active-menu' : '' }}"
                           href="{{ $item['url'] }}">
                            {{ $item['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>


    {{-- Mobile Offcanvas Menu --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="mainMenuLabel">
                {{ $settings->title ?? 'Kino' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column justify-content-between">
            <ul class="navbar-nav mb-4">
                @foreach($menuItems as $item)
                    <li class="nav-item mt-3">
                        <a class="nav-link py-2 fs-6 {{ request()->is(...$item['is_patterns']) ? 'active fw-bold text-primary' : '' }}"
                           href="{{ $item['url'] }}">
                            {{ $item['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="pt-3 mt-auto">
                @guest
                    <div class="d-grid gap-2">
                        <a class="btn btn-primary" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login
                        </a>
                        <a class="btn btn-outline-secondary" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-2"></i> Register
                        </a>
                    </div>
                @else
                    <div class="mb-3 fw-semibold">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ Auth::user()->name }}
                    </div>

                    <div class="d-grid gap-2">
                        @can('viewAny', \App\Models\Film::class)
                            <a class="btn btn-outline-secondary text-start" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Адмінка
                            </a>
                        @endcan

                        <a class="btn btn-outline-secondary text-start" href="{{ route('profile') }}">
                            <i class="bi bi-person me-2"></i> Профіль
                        </a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger w-100 text-start" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </div>


    @if(View::exists('layouts.inc.header.carouselfilms'))
        <section class="pt-3 pb-1 bg-body-tertiary">
            <div class="container">
                @include('layouts.inc.header.carouselfilms')
            </div>
        </section>
    @endif


</header>
