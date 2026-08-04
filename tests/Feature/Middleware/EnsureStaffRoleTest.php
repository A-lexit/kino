<?php
namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsureStaffRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', \App\Http\Middleware\EnsureStaffRole::class])
            ->get('/test-staff-only', function () {
                return response('OK', 200);
            });
    }


    public function test_guest_cannot_access_staff_route(): void
    {
        $response = $this->get('/test-staff-only');

        $response->assertStatus(404);
    }


    public function test_admin_can_access_staff_route(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/test-staff-only');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }


    public function test_editor_can_access_staff_route(): void
    {
        $editor = User::factory()->editor()->create();

        $response = $this->actingAs($editor)->get('/test-staff-only');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }


    public function test_viewer_can_access_staff_route(): void
    {
        $viewer = User::factory()->viewer()->create();

        $response = $this->actingAs($viewer)->get('/test-staff-only');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }


    public function test_regular_user_cannot_access_staff_route(): void
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::User]);

        $response = $this->actingAs($user)->get('/test-staff-only');

        $response->assertStatus(404);
    }

}
