<?php
namespace App\Observers;

use Illuminate\Support\Str;

class SlugObserver
{
    public function creating($model): void
    {
        if (blank($model->slug)) {
            $this->generateSlug($model);
        }
    }

    public function updating($model): void
    {
        if(empty($model->slug)){
            $this->generateSlug($model);
        }
    }

    protected function generateSlug($model): void
    {
        $title = $model->title ?? $model->name ?? null;
        if ($title) {
            $model->slug = Str::slug($title);
        }
    }

}
