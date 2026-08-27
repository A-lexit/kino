@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Мови" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список мов
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($languages->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.languages.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Language::class"
                                        create-route="admin.languages.create"
                                        create-label="Додати мову"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані мови?"
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

                                            @foreach($languages as $language)

                                                <tr id="entity-row-{{ $language->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $language->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $language->id }}
                                                    </td>

                                                    <td>
                                                        {{ $language->title }}
                                                    </td>

                                                    <td>
                                                        {{ $language->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$language"
                                                            edit-route="admin.languages.edit"
                                                            delete-route="admin.languages.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити мову «{{ $language->title }}»?"
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
                                    Мов поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $languages->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
