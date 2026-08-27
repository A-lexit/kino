@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Продюсери" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список продюсерів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($producers->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.producers.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Producer::class"
                                        create-route="admin.producers.create"
                                        create-label="Додати продюсера"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибраних продюсерів?"
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

                                            @foreach($producers as $producer)

                                                <tr id="entity-row-{{ $producer->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $producer->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $producer->id }}
                                                    </td>

                                                    <td>
                                                        {{ $producer->name }}
                                                    </td>

                                                    <td>
                                                        {{ $producer->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$producer"
                                                            edit-route="admin.producers.edit"
                                                            delete-route="admin.producers.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити продюсера «{{ $producer->name }}»?"
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
                                    Продюсерів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $producers->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
