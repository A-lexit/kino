@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Вікові обмеження" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список вікових обмежень
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($ages->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.ages.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Age::class"
                                        create-route="admin.ages.create"
                                        create-label="Додати вікове обмеження"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані вікові обмеження?"
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

                                            @foreach($ages as $age)

                                                <tr id="entity-row-{{ $age->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $age->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $age->id }}
                                                    </td>

                                                    <td>
                                                        {{ $age->title }}
                                                    </td>

                                                    <td>
                                                        {{ $age->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$age"
                                                            edit-route="admin.ages.edit"
                                                            delete-route="admin.ages.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити це вікове обмеження?"
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
                                    Вікових обмежень поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $ages->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
