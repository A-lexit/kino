<header>

    {{-- Верхня інформаційна панель --}}
    <div class="bg-dark text-light small">

        <div class="container">

            <div class="d-flex justify-content-between align-items-center py-2">

                <div class="d-flex align-items-center gap-3 flex-wrap">

                    @if($currentDate)
                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $currentDate->format('d.m.Y') }}
                        </span>
                    @endif

                    @if($currency)
                        <span>
                            USD
                            <strong>{{ $currency['USD'] }}</strong>
                        </span>

                        <span>
                            EUR
                            <strong>{{ $currency['EUR'] }}</strong>
                        </span>
                    @endif

                </div>

                <div>

                    @guest

                        <div class="d-none d-lg-flex gap-3">

                            <a
                                class="text-light text-decoration-none"
                                href="{{ route('login') }}">

                                Login

                            </a>

                            <a
                                class="text-light text-decoration-none"
                                href="{{ route('register') }}">

                                Register

                            </a>

                        </div>

                    @else

                        <div class="dropdown d-none d-lg-block">

                            <a
                                href="#"
                                class="dropdown-toggle text-light text-decoration-none"
                                data-bs-toggle="dropdown">

                                <i class="bi bi-person-circle me-1"></i>

                                {{ Auth::user()->name }}

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">

                                @can('viewAny', \App\Models\Film::class)

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="{{ route('admin.dashboard') }}">

                                            <i class="bi bi-speedometer2 me-2"></i>

                                            Адмінка

                                        </a>

                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                @endcan

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route('profile') }}">

                                        <i class="bi bi-person me-2"></i>

                                        Профіль

                                    </a>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                    <li>

                                        <form action="{{ route('logout') }}" method="POST">

                                            @csrf

                                            <button type="submit" class="dropdown-item">

                                                <i class="bi bi-box-arrow-right me-2"></i>

                                                Logout

                                            </button>

                                        </form>

                                    </li>

                            </ul>

                        </div>

                    @endguest

                </div>

            </div>

        </div>

    </div>

    {{-- Логотип --}}
    <div class="bg-white shadow-sm">

        <div class="container py-3">

            <nav class="navbar navbar-light p-0">

                <a
                    href="{{ route('home') }}"
                    class="navbar-brand m-0">

                    <img
                        src="{{ app(\App\Media\SettingsImageResolver::class)->logo($settings) }}"
                        alt="{{ $settings->title ?? 'Kino' }}"
                        style="height:58px;width:auto;">

                </a>

                <button
                    class="navbar-toggler d-lg-none ms-auto"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mainMenu">

                    <span class="navbar-toggler-icon"></span>

                </button>

            </nav>

        </div>

    </div>

    {{-- Пошук --}}
    <div class="bg-light border-top border-bottom py-3">

        <div class="container">

            <div
                id="search-app"
                class="mx-auto"
                style="max-width:700px;width:100%;">

                <search-component></search-component>

            </div>

        </div>

    </div>


    {{-- Меню (тільки Desktop) --}}
    <nav class="navbar navbar-expand-lg bg-white border-bottom d-none d-lg-flex">

        <div class="container justify-content-center">

            <ul class="navbar-nav">

                @foreach($menuItems as $item)

                    <li class="nav-item">

                        <a
                            class="nav-link px-3 {{ request()->is(...$item['is_patterns']) ? 'active fw-semibold' : '' }}"
                            href="{{ $item['url'] }}">

                            {{ $item['name'] }}

                        </a>

                    </li>

                @endforeach

            </ul>

        </div>

    </nav>


    {{-- Mobile menu --}}
    <div class="offcanvas offcanvas-start"
         tabindex="-1"
         id="mainMenu">

        <div class="offcanvas-header">

            <h5 class="offcanvas-title">

                {{ $settings->title ?? 'Kino' }}

            </h5>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas">
            </button>

        </div>


        <div class="offcanvas-body">

            {{-- Навігація --}}
            <ul class="navbar-nav mb-4">

                @foreach($menuItems as $item)

                    <li class="nav-item">

                        <a
                            class="nav-link py-2 {{ request()->is(...$item['is_patterns']) ? 'active fw-semibold' : '' }}"
                            href="{{ $item['url'] }}"
                            data-bs-dismiss="offcanvas">

                            {{ $item['name'] }}

                        </a>

                    </li>

                @endforeach

            </ul>

            <hr>

            {{-- Авторизація --}}
            @guest

                <div class="d-grid gap-2">

                    <a class="btn btn-dark"
                       href="{{ route('login') }}">

                        <i class="bi bi-box-arrow-in-right me-2"></i>

                        Login

                    </a>

                    <a class="btn btn-outline-dark"
                       href="{{ route('register') }}">

                        <i class="bi bi-person-plus me-2"></i>

                        Register

                    </a>

                </div>

            @else

                <div class="mb-3 fw-semibold">

                    <i class="bi bi-person-circle me-2"></i>

                    {{ Auth::user()->name }}

                </div>

                <div class="d-grid gap-2">

                    @can('viewAny', \App\Models\Film::class)

                        <a class="btn btn-outline-dark"
                           href="{{ route('admin.dashboard') }}">

                            <i class="bi bi-speedometer2 me-2"></i>

                            Адмінка

                        </a>

                    @endcan

                    <a class="btn btn-dark"
                       href="{{ route('profile') }}">

                        <i class="bi bi-person me-2"></i>

                        Профіль

                    </a>

                    <form action="{{ route('logout') }}"
                          method="POST">

                        @csrf

                        <button class="btn btn-outline-danger w-100"
                                type="submit">

                            <i class="bi bi-box-arrow-right me-2"></i>

                            Logout

                        </button>

                    </form>

                </div>

            @endguest

        </div>

    </div>


    {{-- Карусель --}}
    <section class="py-3 bg-light">

        <div class="container">

            @include('layouts.inc.carouselfilms')

        </div>

    </section>

</header>
