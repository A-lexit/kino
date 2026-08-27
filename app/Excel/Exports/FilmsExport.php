<?php
namespace App\Excel\Exports;

use App\Models\Film;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FilmsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Film::with([
            'category',
            'year',
            'duration',
            'quality',
            'season',
            'rating',
            'status',
            'age',
            'user',
            'state',
            'actors',
            'genres',
            'countries'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'title',
            'slug',
            'origin_title',
            'other_actor',
            'note',
            'description',
            'category',
            'year',
            'duration',
            'quality',
            'season',
            'rating',
            'status',
            'age',
            'author',
            'publish_status',
            'is_featured',
            'tmdb_id',
            'imdb_id',
            'imdb_rating',
            'datepicker',
            'thumbnail',
            'tmdb_poster',
            'trailer_youtube_id',
            'trailer_file',
            'gal_image1',
            'gal_image2',
            'gal_image3',
            'gal_image4',
            'gal_image5',
            'likes',
            'views',
            'sort_order',
            'actors',
            'genres',
            'countries',
        ];
    }

    public function map($film): array
    {
        $status = $film->publish_status;
        if (is_object($status)) {
            $status = $status->value ?? $status->name ?? (string) $status;
        }

        return [
            $film->id,
            $film->title,
            $film->slug,
            $film->origin_title,
            $film->other_actor,
            $film->note,
            $film->description,
            $film->category?->title,
            $film->year?->title,
            $film->duration?->title,
            $film->quality?->title,
            $film->season?->title,
            $film->rating?->title,
            $film->status?->title,
            $film->age?->title,
            $film->user?->name,
            $status,
            $film->is_featured ? '1' : '0',
            $film->tmdb_id,
            $film->imdb_id,
            $film->imdb_rating,
            $film->datepicker,
            $film->thumbnail,
            $film->tmdb_poster,
            $film->trailer_youtube_id,
            $film->trailer_file,
            $film->gal_image1,
            $film->gal_image2,
            $film->gal_image3,
            $film->gal_image4,
            $film->gal_image5,
            $film->state?->likes ?? 0,
            $film->state?->views ?? 0,
            $film->sort_order,
            $film->actors->pluck('name')->implode(', '),
            $film->genres->pluck('title')->implode(', '),
            $film->countries->pluck('title')->implode(', '),
        ];
    }
}
