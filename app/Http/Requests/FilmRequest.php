<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $filmId = $this->route('film') ?? $this->route('id');

        return [
            'title' => [
                'required',
                'string',
                'min:2',
                'max:120',
                Rule::unique('films', 'title')->ignore($filmId),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('films', 'slug')->ignore($filmId),
            ],

            // Для draft категорія може бути відсутня.
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'year_id'     => ['nullable', 'integer'],
            'season_id'   => ['nullable', 'integer'],
            'rating_id'   => ['nullable', 'integer'],
            'status_id'   => ['nullable', 'integer'],
            'age_id'      => ['nullable', 'integer'],
            'quality_id'  => ['nullable', 'integer'],
            'duration_id' => ['nullable', 'integer'],
            'datepicker'  => ['nullable', 'string'],

            'trailer_youtube_url' => ['nullable', 'string', 'max:255'],
            'trailer_file'        => ['nullable', 'file', 'mimes:mp4,webm,ogg', 'max:51200'],

            'sort_order'      => ['nullable', 'integer', 'min:0'],
            'related_films'   => ['nullable', 'array'],
            'related_films.*' => ['exists:films,id'],

            'likes' => ['nullable', 'integer', 'min:0'],
            'views' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'Фільм з такою назвою вже існує в базі. Введіть іншу назву.',
            'category_id.exists' => 'Обрана категорія не існує в системі.',
        ];
    }

}
