<?php

namespace Tests\Unit\Models\Film;

use App\Enums\FilmStatus;
use App\Enums\UserRole;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_returns_only_published_films(): void
    {
        $published = Film::factory()->create([
            'publish_status' => FilmStatus::Published,
        ]);

        Film::factory()->create([
            'publish_status' => FilmStatus::Draft,
        ]);

        $films = Film::published()->get();

        $this->assertCount(1, $films);
        $this->assertTrue($films->contains($published));
    }

    public function test_for_user_scope_returns_no_films_for_regular_user(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        Film::factory()->create([
            'author_id' => $user->id,
        ]);

        Film::factory()->create();

        $films = Film::forUser($user)->get();

        $this->assertCount(0, $films);
    }

    public function test_for_user_scope_returns_all_films_for_admin(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Film::factory()->count(3)->create();

        $films = Film::forUser($admin)->get();

        $this->assertCount(3, $films);
    }

    public function test_for_user_scope_returns_all_films_for_editor(): void
    {
        $editor = User::factory()->create([
            'role' => UserRole::Editor,
        ]);

        Film::factory()->count(3)->create();

        $films = Film::forUser($editor)->get();

        $this->assertCount(3, $films);
    }

    public function test_for_user_scope_returns_all_films_for_viewer(): void
    {
        $viewer = User::factory()->create([
            'role' => UserRole::Viewer,
        ]);

        Film::factory()->count(4)->create();

        $films = Film::forUser($viewer)->get();

        $this->assertCount(4, $films);
    }

    public function test_for_user_scope_with_null_user_returns_no_films(): void
    {
        Film::factory()->count(3)->create();

        $films = Film::forUser(null)->get();

        $this->assertCount(0, $films);
    }
}
