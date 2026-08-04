@extends('admin.layouts.layout')
@section('content')


<form method="GET"
      action="{{route('admin.films.search')}}">

    <input
        type="text"
        name="query"
        value="{{ $query ?? '' }}"
        placeholder="Назва фільму"
    />

    <button>
        Пошук
    </button>
</form>


@foreach($movies as $movie)

    <div style="
    display:flex;
    gap:20px;
    margin-bottom:25px;
    padding:15px;
    border:1px solid #ddd;
">

        @if(!empty($movie['poster_path']))
            <img
                width="150"
                src="https://image.tmdb.org/t/p/w200{{ $movie['poster_path'] }}"
            >

        @else
            <div style="width:150px;height:220px;background:#ddd">
                Немає постера
            </div>
        @endif



        <div>
            <h3>
                {{ $movie['title'] }}
            </h3>

            <p>
                {{ $movie['release_date'] ?? 'Дата невідома' }}
            </p>

            <p>
                {{ Str::limit($movie['overview'], 200) }}
            </p>


            <a
                class="btn btn-success"
                href="{{route('admin.films.import.store',$movie['id'])}}">

                Імпортувати
            </a>
        </div>
    </div>

@endforeach
@endsection
