@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Категорії" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список категорій
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($categories->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.categories.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Category::class"
                                        create-route="admin.categories.create"
                                        create-label="Додати категорію"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані категорії?"
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

                                            @foreach($categories as $category)

                                                <tr id="entity-row-{{ $category->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $category->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $category->id }}
                                                    </td>

                                                    <td>
                                                        {{ $category->title }}
                                                    </td>

                                                    <td>
                                                        {{ $category->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$category"
                                                            edit-route="admin.categories.edit"
                                                            delete-route="admin.categories.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити категорію «{{ $category->title }}»?"
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
                                    Категорій поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $categories->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
