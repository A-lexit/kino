<?php
namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Film;
use App\Models\User;

class FilmPolicy
{
    /**
     * Перегляд списку фільмів.
     *
     * Admin  — усі.
     * Editor — усі.
     * Viewer — усі.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::Admin,
            UserRole::Editor,
            UserRole::Viewer,
        ], true);
    }

    /**
     * Перегляд конкретного фільму.
     *
     * Усі три ролі можуть переглядати фільми.
     */
    public function view(User $user, Film $film): bool
    {
        return in_array($user->role, [
            UserRole::Admin,
            UserRole::Editor,
            UserRole::Viewer,
        ], true);
    }

    /**
     * Створення фільму.
     *
     * Тільки Admin.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Редагування фільму.
     *
     * Admin  — може.
     * Editor — може.
     * Viewer — не може.
     */
    public function update(User $user, Film $film): bool
    {
        return in_array($user->role, [
            UserRole::Admin,
            UserRole::Editor,
        ], true);
    }

    /**
     * Переміщення в кошик.
     *
     * Тільки Admin.
     */
    public function delete(User $user, Film $film): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Відновлення з кошика.
     *
     * Тільки Admin.
     */
    public function restore(User $user, Film $film): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Остаточне видалення.
     *
     * Тільки Admin.
     */
    public function forceDelete(User $user, Film $film): bool
    {
        return $user->role === UserRole::Admin;
    }

}
