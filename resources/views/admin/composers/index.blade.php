@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Композитори" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список композиторів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($composers->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.composers.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Composer::class"
                                        create-route="admin.composers.create"
                                        create-label="Додати композитора"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибраних композиторів?"
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

                                            @foreach($composers as $composer)

                                                <tr id="entity-row-{{ $composer->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $composer->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $composer->id }}
                                                    </td>

                                                    <td>
                                                        {{ $composer->name }}
                                                    </td>

                                                    <td>
                                                        {{ $composer->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$composer"
                                                            edit-route="admin.composers.edit"
                                                            delete-route="admin.composers.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити композитора «{{ $composer->name }}»?"
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
                                    Композиторів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $composers->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
