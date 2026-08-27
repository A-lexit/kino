@extends('layouts/layout')
@section('title', $title)
@section('description', $description)
@section('content')

    <div class="container">

        <x-home-section :films="$films" title="Фільми" category-slug="filmi" />
        <x-home-section :films="$serials" title="Серіали" category-slug="seriali" />
        <x-home-section :films="$mults" title="Мультфільми" category-slug="multfilmi" />
        <x-home-section :films="$multserials" title="Мультеріали" category-slug="multseriali" />

    </div>

@endsection
