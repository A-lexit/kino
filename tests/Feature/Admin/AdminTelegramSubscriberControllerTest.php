<?php

namespace Tests\Feature\Admin;

use App\Models\TelegramSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTelegramSubscriberControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()
            ->admin()
            ->create();

        $this->actingAs($this->admin);
    }

    protected function createSubscriber(
        string $chatId,
        bool $isBanned = false,
        string $username = 'test_user'
    ): TelegramSubscriber {
        return TelegramSubscriber::create([
            'chat_id' => $chatId,
            'username' => $username,
            'first_name' => 'Test',
            'is_banned' => $isBanned,
        ]);
    }

    protected function adminChatId(): string
    {
        $chatId = env('TELEGRAM_CHAT_ID');

        $this->assertNotNull(
            $chatId,
            'TELEGRAM_CHAT_ID не заданий для тестового середовища.'
        );

        return (string) $chatId;
    }

    public function test_index_displays_subscribers_list(): void
    {
        $this->createSubscriber('100001', false, 'user1');
        $this->createSubscriber('100002', false, 'user2');
        $this->createSubscriber('100003', false, 'user3');

        $response = $this->get(
            route('admin.telegram.index')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.telegram.index')
            ->assertViewHas('subscribers');

        $this->assertCount(
            3,
            $response->viewData('subscribers')
        );
    }

    public function test_toggle_ban_blocks_subscriber(): void
    {
        $subscriber = $this->createSubscriber(
            '100001',
            false
        );

        $this->assertFalse($subscriber->is_admin);

        $response = $this->post(
            route('admin.telegram.toggle-ban', $subscriber)
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'is_banned' => 1,
            ]);

        $this->assertDatabaseHas('telegram_subscribers', [
            'id' => $subscriber->id,
            'is_banned' => 1,
        ]);
    }

    public function test_toggle_ban_unblocks_subscriber(): void
    {
        $subscriber = $this->createSubscriber(
            '100002',
            true
        );

        $this->assertFalse($subscriber->is_admin);

        $response = $this->post(
            route('admin.telegram.toggle-ban', $subscriber)
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'is_banned' => 0,
            ]);

        $this->assertDatabaseHas('telegram_subscribers', [
            'id' => $subscriber->id,
            'is_banned' => 0,
        ]);
    }

    public function test_admin_subscriber_cannot_be_banned(): void
    {
        $subscriber = $this->createSubscriber(
            $this->adminChatId(),
            false,
            'telegram_admin'
        );

        $subscriber->refresh();

        $this->assertTrue($subscriber->is_admin);

        $response = $this->post(
            route('admin.telegram.toggle-ban', $subscriber)
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Неможливо заблокувати акаунт адміністратора!',
            ]);

        $this->assertDatabaseHas('telegram_subscribers', [
            'id' => $subscriber->id,
            'is_banned' => 0,
        ]);
    }

    public function test_subscriber_can_be_deleted(): void
    {
        $subscriber = $this->createSubscriber(
            '100004',
            false
        );

        $this->assertFalse($subscriber->is_admin);

        $response = $this->delete(
            route('admin.telegram.destroy', $subscriber)
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('telegram_subscribers', [
            'id' => $subscriber->id,
        ]);
    }

    public function test_admin_subscriber_cannot_be_deleted(): void
    {
        $subscriber = $this->createSubscriber(
            $this->adminChatId(),
            false,
            'telegram_admin'
        );

        $subscriber->refresh();

        $this->assertTrue($subscriber->is_admin);

        $response = $this->delete(
            route('admin.telegram.destroy', $subscriber)
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Неможливо видалити акаунт адміністратора!',
            ]);

        $this->assertDatabaseHas('telegram_subscribers', [
            'id' => $subscriber->id,
        ]);
    }
}
