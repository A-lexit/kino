{{-- resources/views/inc/breadcrumbs.blade.php --}}
{{-- Очікує $breadcrumbs = [['title' => 'Головна', 'url' => route('home')], ['title' => 'Категорія', 'url' => '...'], ['title' => 'Поточна сторінка', 'url' => null]] --}}
{{-- Останній елемент (без url або з url === null) рендериться без посилання --}}

@if (!empty($breadcrumbs))
    <nav class="breadcrumbs" aria-label="breadcrumb">
        <ol class="breadcrumbs-list" itemscope itemtype="https://schema.org/BreadcrumbList">
            @foreach ($breadcrumbs as $i => $crumb)
                <li class="breadcrumbs-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    @if (!empty($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" itemprop="item">
                            <span itemprop="name">{{ $crumb['title'] }}</span>
                        </a>
                    @else
                        <span itemprop="name" aria-current="page">{{ $crumb['title'] }}</span>
                    @endif
                    <meta itemprop="position" content="{{ $i + 1 }}">
                </li>
                @if (!$loop->last)
                    <li class="breadcrumbs-sep" aria-hidden="true">/</li>
                @endif
            @endforeach
        </ol>
    </nav>

    @once
        <style>
            .breadcrumbs {
                margin: 0 0 16px;
                font-size: 13px !important;
            }
            .breadcrumbs-list {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .breadcrumbs-item {
                display: flex;
                align-items: center;
                font-size: 13px !important;
            }
            .breadcrumbs-item a {
                color: inherit;
                text-decoration: none;
                opacity: 0.75;
                font-size: 13px !important;
                font-weight: 400 !important;
            }
            .breadcrumbs-item a:hover {
                text-decoration: underline;
                opacity: 1;
            }
            .breadcrumbs-item span[aria-current="page"] {
                opacity: 1;
                font-size: 13px !important;
                font-weight: 500 !important;
            }
            .breadcrumbs-sep {
                opacity: 0.4;
                font-size: 13px !important;
            }
        </style>
    @endonce
@endif
