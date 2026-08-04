@section('title', ($title ?? $settings->title ?? 'Kino') . ' — Kino')
@section('description', \Illuminate\Support\Str::limit(strip_tags($description ?? $settings->description ?? ''), 160))

