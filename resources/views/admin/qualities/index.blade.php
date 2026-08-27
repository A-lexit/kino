@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Якість відео" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список якостей відео
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($qualities->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.qualities.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Quality::class"
                                        create-route="admin.qualities.create"
                                        create-label="Додати якість відео"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані якості відео?"
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

                                            @foreach($qualities as $quality)

                                                <tr id="entity-row-{{ $quality->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $quality->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $quality->id }}
                                                    </td>

                                                    <td>
                                                        {{ $quality->title }}
                                                    </td>

                                                    <td>
                                                        {{ $quality->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$quality"
                                                            edit-route="admin.qualities.edit"
                                                            delete-route="admin.qualities.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити якість відео «{{ $quality->title }}»?"
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
                                    Якостей відео поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $qualities->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection

