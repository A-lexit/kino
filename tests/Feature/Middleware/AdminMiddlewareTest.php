<?php
namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', \App\Http\Middleware\AdminMiddleware::class])
            ->get('/test-admin-only', function () {
                return response('OK', 200);
            });
    }


    public function test_guest_cannot_access_admin_route(): void
    {
        $response = $this->get('/test-admin-only');

        $response->assertStatus(404);
    }


    public function test_non_admin_user_cannot_access_admin_route(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/test-admin-only');

        $response->assertStatus(404);
    }


    public function test_admin_user_can_access_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/test-admin-only');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }

}
