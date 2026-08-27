@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Підписники</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">

                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>

                        <li class="breadcrumb-item active">
                            Список підписників
                        </li>

                    </ol>
                </div>

            </div>
        </div>
    </section>


    <section class="content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список підписників
                            </h3>
                        </div>


                        <div class="card-body">

                            {{-- =====================================================
                                 ДОДАТИ / BULK
                            ====================================================== --}}

                            <div class="mb-3 d-flex align-items-center">

                                @if(auth()->user()?->isAdmin())

                                    <a href="{{ route('admin.subscribers.create') }}"
                                       class="btn btn-success me-2">

                                        Додати підписника

                                    </a>

                                @elseif(auth()->user()?->isViewer())

                                    <button type="button"
                                            class="btn btn-success me-2 opacity-50 cursor-not-allowed"
                                            disabled
                                            title="Додавання підписників доступне лише адміністратору">

                                        Додати підписника

                                    </button>

                                @endif


                                {{-- Bulk delete --}}
                                @if(auth()->user()?->isAdmin())

                                    <button id="global-bulk-delete-btn"
                                            class="btn btn-danger d-none"
                                            type="button"
                                            data-url="{{ route('admin.subscribers.bulk-action') }}">

                                        <i class="bi bi-x-circle"></i>

                                        Видалити вибрані
                                        (<span id="global-selected-count">0</span>)

                                    </button>

                                @elseif(auth()->user()?->isViewer())

                                    <button type="button"
                                            class="btn btn-danger opacity-50 cursor-not-allowed"
                                            disabled
                                            title="Масове видалення доступне лише адміністратору">

                                        <i class="bi bi-x-circle"></i>

                                        Видалити вибрані
                                        (<span id="global-selected-count">0</span>)

                                    </button>

                                @endif

                            </div>


                            @if(count($subs))

                                <div class="table-responsive">

                                    <table id="example1"
                                           class="table table-bordered table-hover text-nowrap">

                                        <thead>
                                        <tr>

                                            {{-- Checkbox --}}
                                            <th style="width: 40px"
                                                class="text-center">

                                                @if(auth()->user()?->isAdmin())

                                                    <input type="checkbox"
                                                           id="global-select-all"
                                                           class="form-check-input">

                                                @elseif(auth()->user()?->isViewer())

                                                    <input type="checkbox"
                                                           class="form-check-input"
                                                           disabled>

                                                @endif

                                            </th>

                                            <th style="width: 30px">
                                                #
                                            </th>

                                            <th>
                                                Email
                                            </th>

                                            <th>
                                                Дії
                                            </th>

                                        </tr>
                                        </thead>


                                        <tbody>

                                        @foreach($subs as $sub)

                                            <tr id="entity-row-{{ $sub->id }}">

                                                {{-- Checkbox --}}
                                                <td class="text-center">

                                                    @if(auth()->user()?->isAdmin())

                                                        <input type="checkbox"
                                                               class="form-check-input entity-checkbox"
                                                               value="{{ $sub->id }}">

                                                    @elseif(auth()->user()?->isViewer())

                                                        <input type="checkbox"
                                                               class="form-check-input entity-checkbox"
                                                               value="{{ $sub->id }}"
                                                               disabled>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $sub->id }}
                                                </td>


                                                <td>
                                                    {{ $sub->email }}
                                                </td>


                                                {{-- Дії --}}
                                                <td class="text-nowrap">

                                                    @if(auth()->user()?->isAdmin())

                                                        <form action="{{ route('admin.subscribers.destroy', ['subscriber' => $sub->id]) }}"
                                                              method="post"
                                                              class="d-inline-block ms-1 ajax-delete-form"
                                                              data-id="{{ $sub->id }}">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="btn btn-danger btn-sm"
                                                                    title="Видалити">

                                                                <i class="bi bi-x-circle"></i>

                                                            </button>

                                                        </form>

                                                    @elseif(auth()->user()?->isViewer())

                                                        <span title="Видалення доступне лише адміністратору">

                                                            <button type="button"
                                                                    class="btn btn-danger btn-sm opacity-50 cursor-not-allowed"
                                                                    disabled>

                                                                <i class="bi bi-x-circle"></i>

                                                            </button>

                                                        </span>
                                                    @endif
                                                </td>

                                            </tr>

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>


                            @else

                                <p>
                                    Підписників поки немає...
                                </p>

                            @endif

                        </div>


                        <div class="card-footer clearfix">
                            {{ $subs->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </section>

@endsection
