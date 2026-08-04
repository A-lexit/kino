<?php

namespace App\Media;

use App\Models\User;

class UserImageResolver
{
    public function image(User $user): string
    {
        if (!$user->avatar) {
            return asset('img/no-image.png');
        }

        return app(ImageMedia::class)->url($user->avatar);
    }

}
