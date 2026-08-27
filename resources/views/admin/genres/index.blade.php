@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Жанри" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список жанрів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($genres->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.genres.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Genre::class"
                                        create-route="admin.genres.create"
                                        create-label="Додати жанр"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані жанри?"
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

                                            @foreach($genres as $genre)

                                                <tr id="entity-row-{{ $genre->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $genre->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $genre->id }}
                                                    </td>

                                                    <td>
                                                        {{ $genre->title }}
                                                    </td>

                                                    <td>
                                                        {{ $genre->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$genre"
                                                            edit-route="admin.genres.edit"
                                                            delete-route="admin.genres.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити жанр «{{ $genre->title }}»?"
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
                                    Жанрів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $genres->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
