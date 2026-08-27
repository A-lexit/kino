@extends('admin.layouts.layout')
@section('content')

    <x-admin.content-header title="Субтитри" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">
                                Список субтитрів
                            </h3>
                        </div>

                        <div class="card-body">

                            @if($captions->isNotEmpty())

                                <div class="table-container"
                                     data-bulk-url="{{ route('admin.captions.bulk-action') }}">

                                    <x-admin.resource.toolbar
                                        :model="\App\Models\Caption::class"
                                        create-route="admin.captions.create"
                                        create-label="Додати субтитри"
                                        bulk-delete-confirm="Ви впевнені, що хочете видалити вибрані субтитри?"
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

                                            @foreach($captions as $caption)

                                                <tr id="entity-row-{{ $caption->id }}">

                                                    {{-- Checkbox --}}
                                                    <td class="text-center {{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">

                                                        <input type="checkbox"
                                                               class="form-check-input bulk-checkbox"
                                                               value="{{ $caption->id }}">

                                                    </td>

                                                    <td>
                                                        {{ $caption->id }}
                                                    </td>

                                                    <td>
                                                        {{ $caption->title }}
                                                    </td>

                                                    <td>
                                                        {{ $caption->slug }}
                                                    </td>

                                                    <td>

                                                        <x-admin.resource.actions
                                                            :item="$caption"
                                                            edit-route="admin.captions.edit"
                                                            delete-route="admin.captions.destroy"
                                                            delete-confirm="Ви впевнені, що хочете видалити субтитри «{{ $caption->title }}»?"
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
                                    Субтитрів поки немає...
                                </p>

                            @endif

                        </div>

                        <div class="card-footer clearfix">
                            {{ $captions->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
