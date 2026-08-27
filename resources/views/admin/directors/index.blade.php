@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Режисери" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список режисерів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($directors->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.directors.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Director::class"
                                        create-route="admin.directors.create"
                                        create-label="Додати режисера"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибраних режисерів?"
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
                                                    Ім'я
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

                                            @foreach($directors as $director)

                                                <tr id="entity-row-{{ $director->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $director->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $director->id }}
                                                    </td>

                                                    <td>
                                                        {{ $director->name }}
                                                    </td>

                                                    <td>
                                                        {{ $director->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$director"
                                                            edit-route="admin.directors.edit"
                                                            delete-route="admin.directors.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити режисера «{{ $director->name }}»?"
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
                                    Режисерів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $directors->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
