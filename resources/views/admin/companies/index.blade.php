@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Компанії" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список компаній
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($companies->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.companies.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Company::class"
                                        create-route="admin.companies.create"
                                        create-label="Додати компанію"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані компанії?"
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

                                            @foreach($companies as $company)

                                                <tr id="entity-row-{{ $company->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $company->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $company->id }}
                                                    </td>

                                                    <td>
                                                        {{ $company->title }}
                                                    </td>

                                                    <td>
                                                        {{ $company->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$company"
                                                            edit-route="admin.companies.edit"
                                                            delete-route="admin.companies.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити компанію «{{ $company->title }}»?"
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
                                    Компаній поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $companies->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
