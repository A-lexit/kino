<?php
namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Film;
use App\Models\User;

class FilmPolicy
{
    /**
     * Admin — бачить/редагує/видаляє всі фільми.
     * Editor — бачить і редагує ТІЛЬКИ свої фільми, видаляти НЕ може (навіть свої).
     * Viewer — бачить усі фільми (тільки перегляд), нічого не може змінювати.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }


    public function view(User $user, Film $film): bool
    {
        return match ($user->role) {
            UserRole::Admin, UserRole::Viewer => true,
            UserRole::Editor => $user->id === $film->author_id,
            default => false,
        };
    }


    public function create(User $user): bool
    {
        // Viewer нічого не створює
        return $user->role === UserRole::Admin || $user->role === UserRole::Editor;
    }


    public function update(User $user, Film $film): bool
    {
        return $user->role === UserRole::Admin
            || ($user->role === UserRole::Editor && $user->id === $film->author_id);
    }


    public function delete(User $user, Film $film): bool
    {
        // М'яке видалення — тільки Admin (Editor не може навіть свої)
        return $user->role === UserRole::Admin;
    }


    public function restore(User $user, Film $film): bool
    {
        return $user->role === UserRole::Admin;
    }


    public function forceDelete(User $user, Film $film): bool
    {
        return $user->role === UserRole::Admin;
    }

}
