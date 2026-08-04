<?php
namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => 0,
            'role' => UserRole::User,
            'is_banned' => 0,
            'avatar' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State for admin user.
     * ВАЖЛИВО: виставляємо саме 'role', а не 'is_admin' —
     * останній автоматично перерахується з role при save() (див. User::booted()).
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin,
        ]);
    }

    /**
     * State for editor user.
     */
    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Editor,
        ]);
    }

    /**
     * State for viewer user.
     */
    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Viewer,
        ]);
    }

    /**
     * State for banned user.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_banned' => 1,
        ]);
    }

}
