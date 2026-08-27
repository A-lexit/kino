@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Тривалість" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Тривалість перегляду
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($durations->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.durations.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Duration::class"
                                        create-route="admin.durations.create"
                                        create-label="Додати тривалість перегляду"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані тривалості?"
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

                                            @foreach($durations as $duration)

                                                <tr id="entity-row-{{ $duration->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $duration->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $duration->id }}
                                                    </td>

                                                    <td>
                                                        {{ $duration->title }}
                                                    </td>

                                                    <td>
                                                        {{ $duration->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$duration"
                                                            edit-route="admin.durations.edit"
                                                            delete-route="admin.durations.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити тривалість «{{ $duration->title }}»?"
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
                                    Тривалостей поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $durations->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
