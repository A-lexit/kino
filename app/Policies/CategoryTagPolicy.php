<?php
namespace App\Policies;

use App\Models\User;

class CategoryTagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isEditor()
            || $user->isViewer();
    }

    public function view(User $user, mixed $reference): bool
    {
        return $user->isAdmin()
            || $user->isEditor()
            || $user->isViewer();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, mixed $reference): bool
    {
        return $user->isAdmin()
            || $user->isEditor();
    }

    public function delete(User $user, mixed $reference): bool
    {
        return $user->isAdmin();
    }

}
