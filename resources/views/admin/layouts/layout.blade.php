<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Адмінка')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.10/dist/css/tempus-dominus.min.css">
    <!-- CSS для DataTables + Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin.css') }}">


    @vite(['resources/js/admin.js'])
    <script src="https://cdn.tiny.cloud/1/txln5vn9q43ycsqx4nexvd0fqki8ytkg41zrl922uzssr29v/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>


    <!-- JS: jQuery (обов'язково для DataTables) та сам плагін -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            tinymce.init({
                selector: 'textarea#description',
                plugins: "lists,advlist,anchor,autolink,autoresize,autosave,bbcode,charmap,code,codesample,directionality,emoticons,fullpage,help,hr,image,imagetools,importcss,insertdatetime,legacyoutput,link,lists,media,nonbreaking,noneditable,pagebreak,paste,preview,print,save,searchreplace,spellchecker,tabfocus,table,template,textpattern,toc,visualblocks,visualchars,wordcount",
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | link image anchor insertdatetime media | emoticons pagebreak | paste searchreplace | toc visualblocks visualchars wordcount |',
                media_filter_html: false
            });
        });
    </script>
    <style>
        .meta-info {
            border: 1px solid #ccc;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 25px;
            padding: 15px;
        }
        .meta-info p {
            margin-bottom: 25px;
        }
        .ck-editor__editable_inline {
            min-height: 300px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
        /* Підганяємо базовий Select2 під вигляд Bootstrap 5 форм AdminLTE4,
           без сторонньої theme-бібліотеки — щоб не було конфліктів стилів */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: var(--bs-border-radius, 0.375rem);
            min-height: calc(1.5em + 0.75rem + 2px);
        }
        .select2-container--default .select2-selection--single {
            display: flex;
            align-items: center;
            padding: 0 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
            line-height: normal;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--bs-primary, #0d6efd);
            color: #fff;
            border: none;
            border-radius: 0.25rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            opacity: 0.8;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--bs-primary, #0d6efd);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper mb-5">
    {{-- Navbar --}}
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a href="{{ url('/') }}" target="_blank" class="nav-link">
                        <i class="bi bi-box-arrow-up-right"></i> На сайт
                    </a>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="adminUserDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="bi bi-person-circle"></i>
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ url('/profile') }}">
                                    <i class="bi bi-person"></i>
                                    Профіль
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

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

                        <form id="logout-form"
                              action="{{ route('logout') }}"
                              method="POST"
                              class="d-none">
                            @csrf
                        </form>
                    </li>
                @endauth

            </ul>
        </div>
    </nav>
    {{-- Sidebar --}}
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <span class="brand-text fw-light">Kino Admin</span>
            </a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="nav-icon bi bi-house"></i>
                            <p>Головна</p>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-film"></i>
                            <p>Фільми <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.films.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список фільмів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Film::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.films.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Додати новий фільм</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Додавання фільмів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Додати новий фільм</p>
            </button>
        </span>
                                </li>

                            @endcan

                            @can('create', \App\Models\Film::class)
                                <li class="nav-item">
                                    <a href="{{ route('admin.films.search') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Імпорт з themoviedb</p>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-tags"></i>
                            <p>Категорії <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список категорій</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Category::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.categories.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нова категорія</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення категорій доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нова категорія</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    <li class="nav-item mt-4">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Топ-актори <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.actors.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список акторів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Actor::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.actors.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий актор</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення акторів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий актор</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-person-video"></i>
                            <p>Режисери <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.directors.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список режисерів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Director::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.directors.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий режисер</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення режисерів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий режисер</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-person-workspace"></i>
                            <p>Продюсери <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.producers.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список продюсерів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Producer::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.producers.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий продюсер</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення продюсерів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий продюсер</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-music-note-list"></i>
                            <p>Композитори <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.composers.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список композиторів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Composer::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.composers.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий композитор</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення композиторів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий композитор</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>


                    <li class="nav-item mt-4">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-shield-lock"></i>
                            <p>Вікові обмеження <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.ages.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список обмежень</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Age::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.ages.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нове обмеження</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення вікових обмежень доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нове обмеження</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>




                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-stack"></i>
                            <p>Добірки <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.selections.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список добірок</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Selection::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.selections.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нова добірка</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення добірок доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нова добірка</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-bookmarks"></i>
                            <p>Жанри <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.genres.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список жанрів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Genre::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.genres.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий жанр</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення жанрів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий жанр</p>
            </button>
        </span>
                                </li>
                            @endcan
                        </ul>
                    </li>




                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-building"></i>
                            <p>Компанії <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.companies.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список компаній</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Company::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.companies.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нова компанія</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення компаній доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нова компанія</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-globe2"></i>
                            <p>Країни <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.countries.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список країн</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Country::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.countries.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нова країна</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення країн доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нова країна</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-translate"></i>
                            <p>Мови <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.languages.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список мов</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Language::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.languages.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нова мова</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення мов доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нова мова</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>






                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-star"></i>
                            <p>Рейтинги <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.ratings.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список рейтингів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Rating::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.ratings.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий рейтинг</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення рейтингів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий рейтинг</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>





                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-calendar3"></i>
                            <p>Роки випуску<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.years.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список років випуску</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Year::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.years.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Додати рік випуску</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення років доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Додати рік випуску</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>





                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-collection-play"></i>
                            <p>Сезони <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.seasons.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список сезонів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Season::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.seasons.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Додати сезон</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення сезонів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Додати сезон</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>




                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-card-checklist"></i>
                            <p>Статуси серіалів<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.statuses.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список статусів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Status::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.statuses.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Новий статус</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення статусів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Новий статус</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>











                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-badge-cc"></i>
                            <p>Субтитри <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.captions.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список субтитрів</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Caption::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.captions.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нові субтитри</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення субтитрів доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нові субтитри</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-clock"></i>
                            <p>Тривалість <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.durations.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Список тривалостей</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Duration::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.durations.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нова тривалість</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення тривалостей доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Нова тривалість</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-badge-hd"></i>
                            <p>Якість відео <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.qualities.index') }}" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Якості відео</p>
                                </a>
                            </li>
                            @can('create', \App\Models\Quality::class)

                                <li class="nav-item">
                                    <a href="{{ route('admin.qualities.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Додати якість відео</p>
                                    </a>
                                </li>

                            @elseif(auth()->user()?->isViewer())

                                <li class="nav-item">
        <span title="Створення якостей відео доступне лише адміністратору">
            <button type="button"
                    class="nav-link border-0 bg-transparent w-100 text-start opacity-50"
                    disabled>
                <i class="nav-icon bi bi-circle"></i>
                <p>Додати якість відео</p>
            </button>
        </span>
                                </li>

                            @endcan
                        </ul>
                    </li>



                    @if(!auth()->user()?->isEditor())

                        {{-- =====================================================
                             КОРИСТУВАЧІ
                        ====================================================== --}}

                        <li class="nav-item mt-4">

                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-person-lines-fill"></i>

                                <p>
                                    Користувачі
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                {{-- Список користувачів --}}
                                <li class="nav-item">

                                    @if(auth()->user()?->isAdmin())

                                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                                            <i class="nav-icon bi bi-circle"></i>
                                            <p>Список користувачів</p>
                                        </a>

                                    @elseif(auth()->user()?->isViewer())

                                        <span class="nav-link opacity-50 cursor-not-allowed"
                                              title="Розділ користувачів недоступний у демо">

                        <i class="nav-icon bi bi-circle"></i>

                        <p>Список користувачів</p>

                    </span>

                                    @endif

                                </li>


                                <li class="nav-item">

                                    @if(auth()->user()?->isAdmin())

                                        <a href="{{ route('admin.users.create') }}" class="nav-link">
                                            <i class="nav-icon bi bi-circle"></i>
                                            <p>Новий користувач</p>
                                        </a>

                                    @elseif(auth()->user()?->isViewer())

                                        <span class="nav-link opacity-50 cursor-not-allowed"
                                              title="Створення користувачів недоступне у демо">

                        <i class="nav-icon bi bi-circle"></i>

                        <p>Новий користувач</p>

                    </span>

                                    @endif

                                </li>

                            </ul>

                        </li>


                        {{-- =====================================================
                             ПІДПИСНИКИ
                        ====================================================== --}}

                        <li class="nav-item">

                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-envelope"></i>

                                <p>
                                    Підписники
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                <li class="nav-item">

                                    @if(auth()->user()?->isAdmin())

                                        <a href="{{ route('admin.subscribers.index') }}" class="nav-link">
                                            <i class="nav-icon bi bi-circle"></i>
                                            <p>Список підписників</p>
                                        </a>

                                    @elseif(auth()->user()?->isViewer())

                                        <span class="nav-link opacity-50 cursor-not-allowed"
                                              title="Розділ підписників недоступний у демо">

                        <i class="nav-icon bi bi-circle"></i>

                        <p>Список підписників</p>

                    </span>

                                    @endif

                                </li>


                                <li class="nav-item">

                                    @if(auth()->user()?->isAdmin())

                                        <a href="{{ route('admin.subscribers.create') }}" class="nav-link">
                                            <i class="nav-icon bi bi-circle"></i>
                                            <p>Новий підписник</p>
                                        </a>

                                    @elseif(auth()->user()?->isViewer())

                                        <span class="nav-link opacity-50 cursor-not-allowed"
                                              title="Створення підписників недоступне у демо">

                        <i class="nav-icon bi bi-circle"></i>

                        <p>Новий підписник</p>

                    </span>

                                    @endif

                                </li>


                                {{-- Telegram підписники --}}
                                <li class="nav-item mb-5">

                                    @if(auth()->user()?->isAdmin())

                                        <a href="{{ route('admin.telegram.index') }}" class="nav-link">
                                            <i class="nav-icon bi bi-telegram"></i>
                                            <p>Telegram підписники</p>
                                        </a>

                                    @elseif(auth()->user()?->isViewer())

                                        <span class="nav-link opacity-50 cursor-not-allowed"
                                              title="Telegram-підписники недоступні у демо">

                        <i class="nav-icon bi bi-telegram"></i>

                        <p>Telegram підписники</p>

                    </span>

                                    @endif

                                </li>

                            </ul>

                        </li>




                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-chat-dots"></i>
                                <p>Коментарі <i class="nav-arrow bi bi-chevron-right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.comments.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Список коментарів</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-menu-button-wide"></i>

                                <p>
                                    Меню
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.menu.edit') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Нове меню</p>
                                    </a>
                                </li>
                            </ul>
                        </li>



                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear"></i>
                                <p>
                                    Налаштування
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Налаштування</p>
                                    </a>
                                </li>
                            </ul>
                        </li>




                    @endif




                </ul>
            </nav>
        </div>
    </aside>
    {{-- Main content --}}
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <h3 class="mb-0">@yield('title', 'Головна')</h3>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @yield('content')
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function () {
        $('.select2').select2({
            width: '100%',
        });
    });
</script>

<script>
    const bulkActionUrl = "{{ route('admin.films.bulk-action') }}";

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function initFilmsDataTable() {
        const $table = $('#filmsTable');
        if (!$table.length || typeof $.fn.DataTable === 'undefined') {
            return;
        }

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        $table.DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/uk.json'
            },
            pageLength: 15,
            lengthChange: true,
            autoWidth: false,
            columnDefs: [
                {
                    orderable: false, targets: [0, -1] // 0 = чекбокс, -1 = остання колонка ("Дії")
                }
            ]
        });
    }

    function refreshFilmTables(data) {
        const activeSection = document.getElementById('active-films-section');
        const trashSection = document.getElementById('trash-films-section');

        if (activeSection && data.activeHtml !== undefined) {
            activeSection.innerHTML = data.activeHtml;
            initTableContainer(activeSection);
        }

        if (trashSection && data.trashHtml !== undefined) {
            trashSection.innerHTML = data.trashHtml;
            initTableContainer(trashSection);
        }

        initFilmsDataTable();
    }

    function initTableContainer(container) {
        if (!container) return;

        const selectAllCheckbox = container.querySelector('.select-all-checkbox');
        const bulkButtons = container.querySelectorAll('.bulk-action-btn');
        const rowButtons = container.querySelectorAll('.row-action-btn');
        const wholeTableButtons = container.querySelectorAll('.admin-action-btn');

        container.querySelectorAll('.bulk-checkbox, .select-all-checkbox').forEach(cb => {
            cb.checked = false;
        });

        function updateBulkContainerState() {
            const checkboxes = container.querySelectorAll('.bulk-checkbox');
            const checkedCount = container.querySelectorAll('.bulk-checkbox:checked').length;

            bulkButtons.forEach(btn => {
                const countSpan = btn.querySelector('.selected-count');
                if (countSpan) {
                    countSpan.textContent = checkedCount;
                }
                btn.classList.toggle('d-none', checkedCount === 0);
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                container.querySelectorAll('.bulk-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateBulkContainerState();
            });
        }

        container.addEventListener('change', function (e) {
            if (e.target.classList.contains('bulk-checkbox')) {
                updateBulkContainerState();
            }
        });

        function sendAjaxAction(ids, action, confirmMessage, rowsToFade) {
            if (ids.length === 0) return;
            if (confirmMessage && !confirm(confirmMessage)) return;

            const url = bulkActionUrl + window.location.search;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids: ids, action: action })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        rowsToFade.forEach(row => {
                            if (!row) return;
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                        });

                        setTimeout(() => refreshFilmTables(data), 300);
                    } else {
                        alert(data.message || 'Сталася помилка під час виконання дії.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Помилка відправки запиту. Перевірте мережу або маршрути.');
                });
        }

        bulkButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const action = this.dataset.action;
                const confirmMessage = this.dataset.confirm;
                const checkedCheckboxes = container.querySelectorAll('.bulk-checkbox:checked');
                const ids = Array.from(checkedCheckboxes).map(cb => cb.value);
                const rows = Array.from(checkedCheckboxes).map(cb => cb.closest('tr'));

                sendAjaxAction(ids, action, confirmMessage, rows);
            });
        });

        rowButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const id = this.dataset.id;
                const action = this.dataset.action;
                const confirmMessage = this.dataset.confirm;
                const url = this.dataset.url;
                const method = this.dataset.method || 'POST';

                if (confirmMessage && !confirm(confirmMessage)) {
                    return;
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {

                            const row = this.closest('tr');

                            if (row) {
                                row.style.transition = 'opacity 0.3s ease';
                                row.style.opacity = '0';
                            }

                            setTimeout(() => {
                                if (row) {
                                    row.remove();
                                }
                            }, 300);

                        } else {
                            alert(data.message || 'Сталася помилка під час виконання дії.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Помилка відправки запиту.');
                    });
            });
        });

        wholeTableButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const url = this.dataset.url + window.location.search;
                const confirmMessage = this.dataset.confirm;

                if (confirmMessage && !confirm(confirmMessage)) return;

                fetch(url, {
                    method: this.dataset.method || 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            refreshFilmTables(data);
                        } else {
                            alert(data.message || 'Сталася помилка під час виконання дії.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Помилка відправки запиту.');
                    });
            });
        });
    }

    document.querySelectorAll('.table-container').forEach(initTableContainer);
    initFilmsDataTable();
</script>

@stack('scripts')
</body>
</html>
