@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Країни" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список країн
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($countries->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.countries.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Country::class"
                                        create-route="admin.countries.create"
                                        create-label="Додати країну"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані країни?"
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

                                            @foreach($countries as $country)

                                                <tr id="entity-row-{{ $country->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $country->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $country->id }}
                                                    </td>

                                                    <td>
                                                        {{ $country->title }}
                                                    </td>

                                                    <td>
                                                        {{ $country->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$country"
                                                            edit-route="admin.countries.edit"
                                                            delete-route="admin.countries.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити країну «{{ $country->title }}»?"
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
                                    Країн поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $countries->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
