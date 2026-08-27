@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Статуси серіалів та мультсеріалів" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список статусів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($statuses->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.statuses.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Status::class"
                                        create-route="admin.statuses.create"
                                        create-label="Додати статус"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані статуси?"
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

                                            @foreach($statuses as $status)

                                                <tr id="entity-row-{{ $status->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $status->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $status->id }}
                                                    </td>

                                                    <td>
                                                        {{ $status->title }}
                                                    </td>

                                                    <td>
                                                        {{ $status->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$status"
                                                            edit-route="admin.statuses.edit"
                                                            delete-route="admin.statuses.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити статус «{{ $status->title }}»?"
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
                                    Статусів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $statuses->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
