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
                            {{ $homeTitle ?? 'Головна' }}
                        </a>
                    </li>

                    @isset($breadcrumbTitle)
                        <li class="breadcrumb-item active">
                            {{ $breadcrumbTitle }}
                        </li>
                    @else
                        <li class="breadcrumb-item active">
                            {{ $title }}
                        </li>
                    @endisset

                </ol>
            </div>

        </div>
    </div>
</section>

