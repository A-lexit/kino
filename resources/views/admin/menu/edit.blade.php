@extends('admin.layouts.layout')
@section('content')

    <div class="container">
        <h1>Меню сайту</h1>

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        @php
            // Формуємо масив збережених елементів
            // у їхньому актуальному порядку з бази.
            $initialOrder = $menu->items
                ->map(function ($i) {
                    return $i->type . ':' .
                        ($i->type === 'category'
                            ? $i->category_id
                            : $i->static_key);
                })
                ->values();
        @endphp


        <form action="{{ route('admin.menu.update') }}"
              method="POST">

            @csrf
            @method('PUT')


            <div class="form-group">

                <label for="items">
                    Пункти меню (обирай у тому порядку,
                    у якому вони мають з'явитись у шапці сайту):
                </label>


                <select id="items"
                        name="items[]"
                        multiple="multiple"
                        class="form-control">

                    <optgroup label="Сторінки">

                        @foreach($staticPages as $key => $page)

                            <option value="static:{{ $key }}">
                                {{ $page['label'] }}
                            </option>

                        @endforeach

                    </optgroup>


                    <optgroup label="Категорії">

                        @foreach($allCategories as $category)

                            <option value="category:{{ $category->id }}">
                                {{ $category->title }}
                            </option>

                        @endforeach

                    </optgroup>

                </select>


                <small class="form-text text-muted">
                    Порядок вибору в списку = порядок пунктів у меню сайту.
                </small>

            </div>


            <div class="mt-3">

                @if(auth()->user()?->isAdmin())

                    <button type="submit"
                            class="btn btn-primary">
                        Зберегти
                    </button>

                @elseif(auth()->user()?->isViewer())

                    <button type="button"
                            class="btn btn-primary opacity-50 cursor-not-allowed"
                            disabled
                            title="Зміна меню доступна лише адміністратору">
                        Зберегти
                    </button>

                @endif

            </div>

        </form>

    </div>


    @push('scripts')

        <script>
            $(document).ready(function () {

                const $select = $('#items');
                const initialOrder = @json($initialOrder);

                // 1. Спочатку переміщуємо збережені <option>
                // у їхньому порядку з бази.
                initialOrder.forEach(function (val) {

                    const $option = $select.find(
                        'option[value="' + val + '"]'
                    );

                    if ($option.length) {

                        $option.prop('selected', true);

                        $select.append($option);
                    }
                });


                // 2. Ініціалізуємо Select2.
                $select.select2();


                // 3. Новий вибраний елемент переміщуємо в кінець DOM.
                $select.on('select2:select', function (e) {

                    const id = e.params.data.id;

                    const $option = $select.find(
                        'option[value="' + id + '"]'
                    );

                    $option.detach();

                    $select.append($option);

                    $select.trigger('change');

                });

            });
        </script>
    @endpush
@endsection
