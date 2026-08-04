<?php
namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMainControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Гість перенаправляється на сторінку входу.
     */
    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    /**
     * Адміністратор може відкрити головну сторінку адмінпанелі.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }

}
