@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Сезони" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список сезонів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($seasons->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.seasons.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Season::class"
                                        create-route="admin.seasons.create"
                                        create-label="Додати сезон"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані сезони?"
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

                                            @foreach($seasons as $season)

                                                <tr id="entity-row-{{ $season->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $season->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $season->id }}
                                                    </td>

                                                    <td>
                                                        {{ $season->title }}
                                                    </td>

                                                    <td>
                                                        {{ $season->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$season"
                                                            edit-route="admin.seasons.edit"
                                                            delete-route="admin.seasons.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити сезон «{{ $season->title }}»?"
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
                                    Сезонів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $seasons->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
