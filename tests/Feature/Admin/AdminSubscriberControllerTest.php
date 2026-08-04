<?php
namespace Tests\Feature\Admin;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSubscriberControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Створюємо адміністратора для тестування успішних сценаріїв
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * БЛОК 1: ТЕСТИ БЕЗПЕКИ (МАСКУВАННЯ АДМІНКИ ЧЕРЕЗ 404)
     */

    public function test_guest_cannot_access_subscribers_routes(): void
    {
        $this->get(route('admin.subscribers.index'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.subscribers.store'), [
            'email' => 'test@test.com',
        ])->assertRedirect(route('login'));

        $this->delete(route('admin.subscribers.destroy', 1))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_subscribers_routes(): void
    {
        $regularUser = User::factory()->create([
            'is_admin' => 0,
            'is_banned' => 0,
        ]);

        $this->actingAs($regularUser);

        $this->get(route('admin.subscribers.index'))
            ->assertStatus(404);

        $this->post(route('admin.subscribers.store'), [
            'email' => 'test@test.com',
        ])->assertStatus(404);

        $this->delete(route('admin.subscribers.destroy', 1))
            ->assertStatus(404);
    }

    /**
     * БЛОК 2: ОСНОВНИЙ ФУНКЦІОНАЛ (HAPPY PATHS)
     */

    public function test_index_displays_subscribers_list(): void
    {
        $this->actingAs($this->admin);
        Subscription::factory()->count(3)->create();

        $response = $this->get(route('admin.subscribers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subs.index');
        $response->assertViewHas('subs');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.subscribers.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subs.create');
    }

    public function test_store_creates_subscriber_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $email = 'subscriber@example.com';

        $response = $this->post(route('admin.subscribers.store'), ['email' => $email]);

        $response->assertRedirect(route('admin.subscribers.index'));
        $response->assertSessionHas('success', 'Підписника додано');
        $this->assertDatabaseHas('subscriptions', ['email' => $email]);
    }

    /**
     * БЛОК 3: ВАЛІДАЦІЯ ТА КРИТИЧНІ СИТУАЦІЇ (EDGE CASES)
     */

    public function test_store_fails_if_email_is_missing(): void
    {
        $this->actingAs($this->admin);

        // Перевірка правила required
        $response = $this->post(route('admin.subscribers.store'), ['email' => '']);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_store_fails_if_email_is_invalid(): void
    {
        $this->actingAs($this->admin);

        // Перевірка правила email
        $response = $this->post(route('admin.subscribers.store'), ['email' => 'not-an-email']);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_store_fails_if_email_is_duplicate(): void
    {
        $this->actingAs($this->admin);
        Subscription::factory()->create(['email' => 'duplicate@example.com']);

        // Перевірка правила unique
        $response = $this->post(route('admin.subscribers.store'), ['email' => 'duplicate@example.com']);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_destroy_deletes_subscriber_and_returns_json(): void
    {
        $this->actingAs($this->admin);

        $sub = Subscription::factory()->create();

        $response = $this->delete(route('admin.subscribers.destroy', $sub->id));

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Підписника успішно видалено.',
            ]);

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $sub->id,
        ]);
    }

    public function test_destroy_returns_404_for_non_existent_subscriber(): void
    {
        $this->actingAs($this->admin);

        // Спроба видалити несучий ID має викликати ModelNotFoundException (код 404)
        $response = $this->delete(route('admin.subscribers.destroy', 99999));
        $response->assertStatus(404);
    }

}
