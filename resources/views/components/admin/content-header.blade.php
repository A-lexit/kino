@props([
    'title',
    'homeUrl' => null,
    'homeTitle' => 'Головна',
    'breadcrumbTitle' => null,
])

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>{{ $title }}</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">

                    <li class="breadcrumb-item">
                        <a href="{{ $homeUrl ?? route('admin.dashboard') }}">
                            {{ $homeTitle }}
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{ $breadcrumbTitle ?? $title }}
                    </li>

                </ol>
            </div>

        </div>
    </div>
</section>
