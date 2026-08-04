<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Перевіряє на null, порожній рядок "" або просто пробіли "   "
        if (blank($this->title)) {
            $this->merge([
                'title' => 'Невідомий фільм ' . uniqid(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:120'],
            'category_id' => ['integer'],
            'year_id' => ['integer'],
            'season_id' => ['integer'],
            'rating_id' => ['integer'],
            'status_id' => ['integer'],
            'age_id' => ['integer'],
            'quality_id' => ['integer'],
            'duration_id' => ['integer'],
            'view' => ['integer'],
            'datepicker'  => ['nullable', 'string'],

            'trailer_youtube_url' => 'nullable|string|max:255',
            'trailer_file'         => 'nullable|file|mimes:mp4,webm,ogg|max:51200', // 50MB
        ];
    }

}
