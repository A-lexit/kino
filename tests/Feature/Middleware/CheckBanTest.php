<?php
namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CheckBanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', \App\Http\Middleware\CheckBan::class])
            ->get('/test-check-ban', function () {
                return response('OK', 200);
            });
    }


    public function test_guest_passes_through(): void
    {
        $response = $this->get('/test-check-ban');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }


    public function test_non_banned_user_passes_through(): void
    {
        $user = User::factory()->create(['is_banned' => 0]);

        $response = $this->actingAs($user)->get('/test-check-ban');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }


    public function test_banned_user_is_logged_out_and_redirected(): void
    {
        $user = User::factory()->create(['is_banned' => 1]);

        $response = $this->actingAs($user)->get('/test-check-ban');

        $response->assertRedirect('login');
        $response->assertSessionHas('error', 'Ваш акаунт заблоковано');
        $this->assertGuest();
    }

}
