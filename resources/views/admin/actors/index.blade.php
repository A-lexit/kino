@extends('admin.layouts.layout')

@section('content')

    <x-admin.content-header title="Топ-актори" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список топ-акторів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($actors->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.actors.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Actor::class"
                                        create-route="admin.actors.create"
                                        create-label="Додати топ-актора"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибраних акторів?"
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

                                            @foreach($actors as $actor)

                                                <tr id="entity-row-{{ $actor->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $actor->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $actor->id }}
                                                    </td>

                                                    <td>
                                                        {{ $actor->name }}
                                                    </td>

                                                    <td>
                                                        {{ $actor->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$actor"
                                                            edit-route="admin.actors.edit"
                                                            delete-route="admin.actors.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити актора «{{ $actor->name }}»?"
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
                                    Акторів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $actors->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
