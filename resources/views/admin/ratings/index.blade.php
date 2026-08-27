@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Рейтинги" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список рейтингів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($ratings->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.ratings.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Rating::class"
                                        create-route="admin.ratings.create"
                                        create-label="Додати рейтинг"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані рейтинги?"
                                    />

                                    <div class="table-responsive mt-3">

                                        <table class="table table-bordered table-hover text-nowrap">

                                            <thead>
                                            <tr>

                                                {{-- Checkbox --}}
                                                <th style="width: 40px; text-align: center;"
                                                    class="{{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                    <input type="checkbox"
                                                           class="form-check-input select-all-checkbox">

                                                </th>

                                                <th style="width: 30px">
                                                    #
                                                </th>

                                                <th>
                                                    Назва
                                                </th>

                                                <th>
                                                    Slug
                                                </th>

                                                <th>
                                                    Дії
                                                </th>

                                            </tr>
                                            </thead>

                                            <tbody>

                                            @foreach($ratings as $rating)

                                                <tr id="entity-row-{{ $rating->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $rating->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $rating->id }}
                                                    </td>

                                                    <td>
                                                        {{ $rating->title }}
                                                    </td>

                                                    <td>
                                                        {{ $rating->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$rating"
                                                            edit-route="admin.ratings.edit"
                                                            delete-route="admin.ratings.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити рейтинг «{{ $rating->title }}»?"
                                                        />

                                                    </td>

                                                </tr>

                                            @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            @else

                                <p class="text-muted">
                                    Рейтингів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $ratings->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
