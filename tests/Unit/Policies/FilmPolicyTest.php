<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Film;
use App\Models\User;
use App\Policies\FilmPolicy;
use Tests\TestCase;

class FilmPolicyTest extends TestCase
{
    private FilmPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new FilmPolicy();
    }

    private function makeUser(
        UserRole $role,
        int $id = 1
    ): User {
        $user = new User();

        $user->id = $id;
        $user->role = $role;

        return $user;
    }

    private function makeFilm(
        int $authorId = 1
    ): Film {
        return new Film([
            'author_id' => $authorId,
        ]);
    }

    public function test_view_any(): void
    {
        $this->assertTrue(
            $this->policy->viewAny(
                $this->makeUser(UserRole::Admin)
            )
        );

        $this->assertTrue(
            $this->policy->viewAny(
                $this->makeUser(UserRole::Editor)
            )
        );

        $this->assertTrue(
            $this->policy->viewAny(
                $this->makeUser(UserRole::Viewer)
            )
        );

        $this->assertFalse(
            $this->policy->viewAny(
                $this->makeUser(UserRole::User)
            )
        );
    }

    public function test_view(): void
    {
        $film = $this->makeFilm(10);

        // Admin бачить будь-який фільм.
        $this->assertTrue(
            $this->policy->view(
                $this->makeUser(UserRole::Admin),
                $film
            )
        );

        // Editor бачить будь-який фільм,
        // незалежно від author_id.
        $this->assertTrue(
            $this->policy->view(
                $this->makeUser(UserRole::Editor, 10),
                $film
            )
        );

        $this->assertTrue(
            $this->policy->view(
                $this->makeUser(UserRole::Editor, 5),
                $film
            )
        );

        // Viewer бачить будь-який фільм.
        $this->assertTrue(
            $this->policy->view(
                $this->makeUser(UserRole::Viewer),
                $film
            )
        );

        // Звичайний користувач не має доступу в адмінку.
        $this->assertFalse(
            $this->policy->view(
                $this->makeUser(UserRole::User),
                $film
            )
        );
    }

    public function test_create(): void
    {
        // Створення доступне тільки Admin.
        $this->assertTrue(
            $this->policy->create(
                $this->makeUser(UserRole::Admin)
            )
        );

        $this->assertFalse(
            $this->policy->create(
                $this->makeUser(UserRole::Editor)
            )
        );

        $this->assertFalse(
            $this->policy->create(
                $this->makeUser(UserRole::Viewer)
            )
        );

        $this->assertFalse(
            $this->policy->create(
                $this->makeUser(UserRole::User)
            )
        );
    }

    public function test_update(): void
    {
        $film = $this->makeFilm(15);

        // Admin може редагувати будь-який фільм.
        $this->assertTrue(
            $this->policy->update(
                $this->makeUser(UserRole::Admin),
                $film
            )
        );

        // Editor може редагувати власний фільм.
        $this->assertTrue(
            $this->policy->update(
                $this->makeUser(UserRole::Editor, 15),
                $film
            )
        );

        // Editor може редагувати і чужий фільм.
        $this->assertTrue(
            $this->policy->update(
                $this->makeUser(UserRole::Editor, 20),
                $film
            )
        );

        // Viewer — тільки перегляд.
        $this->assertFalse(
            $this->policy->update(
                $this->makeUser(UserRole::Viewer),
                $film
            )
        );

        // Звичайний користувач не має доступу.
        $this->assertFalse(
            $this->policy->update(
                $this->makeUser(UserRole::User),
                $film
            )
        );
    }

    public function test_delete(): void
    {
        $film = $this->makeFilm();

        $this->assertTrue(
            $this->policy->delete(
                $this->makeUser(UserRole::Admin),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->delete(
                $this->makeUser(UserRole::Editor),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->delete(
                $this->makeUser(UserRole::Viewer),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->delete(
                $this->makeUser(UserRole::User),
                $film
            )
        );
    }

    public function test_restore(): void
    {
        $film = $this->makeFilm();

        $this->assertTrue(
            $this->policy->restore(
                $this->makeUser(UserRole::Admin),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->restore(
                $this->makeUser(UserRole::Editor),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->restore(
                $this->makeUser(UserRole::Viewer),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->restore(
                $this->makeUser(UserRole::User),
                $film
            )
        );
    }

    public function test_force_delete(): void
    {
        $film = $this->makeFilm();

        $this->assertTrue(
            $this->policy->forceDelete(
                $this->makeUser(UserRole::Admin),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->forceDelete(
                $this->makeUser(UserRole::Editor),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->forceDelete(
                $this->makeUser(UserRole::Viewer),
                $film
            )
        );

        $this->assertFalse(
            $this->policy->forceDelete(
                $this->makeUser(UserRole::User),
                $film
            )
        );
    }
}
