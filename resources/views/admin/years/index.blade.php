@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Роки випуску" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список років випуску
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($years->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.years.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Year::class"
                                        create-route="admin.years.create"
                                        create-label="Додати рік випуску"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані роки випуску?"
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

                                            @foreach($years as $year)

                                                <tr id="entity-row-{{ $year->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $year->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $year->id }}
                                                    </td>

                                                    <td>
                                                        {{ $year->title }}
                                                    </td>

                                                    <td>
                                                        {{ $year->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$year"
                                                            edit-route="admin.years.edit"
                                                            delete-route="admin.years.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити рік «{{ $year->title }}»?"
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
                                    Років поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $years->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
