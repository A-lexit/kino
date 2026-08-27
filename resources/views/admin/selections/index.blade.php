@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Добірки" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список добірок
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($selections->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.selections.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Selection::class"
                                        create-route="admin.selections.create"
                                        create-label="Додати добірку"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані добірки?"
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

                                            @foreach($selections as $selection)

                                                <tr id="entity-row-{{ $selection->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $selection->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $selection->id }}
                                                    </td>

                                                    <td>
                                                        {{ $selection->title }}
                                                    </td>

                                                    <td>
                                                        {{ $selection->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$selection"
                                                            edit-route="admin.selections.edit"
                                                            delete-route="admin.selections.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити добірку «{{ $selection->title }}»?"
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
                                    Добірок поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $selections->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
