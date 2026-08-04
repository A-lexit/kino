<?php
namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_successful_response(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.settings.index');
    }

    public function test_index_returns_null_settings_when_none_exist(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertStatus(200)
            ->assertViewHas('settings', null);
    }

    public function test_index_returns_view_with_existing_settings(): void
    {
        $settings = Setting::factory()->create(['title' => 'Тестовий заголовок']);

        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertStatus(200)
            ->assertViewHas('settings', $settings);
    }

    public function test_update_saves_settings(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'title'       => 'Новий заголовок',
                'description' => 'Новий опис',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Налаштування збережено');

        $this->assertDatabaseHas('settings', [
            'title'       => 'Новий заголовок',
            'description' => 'Новий опис',
        ]);
    }

    public function test_update_creates_settings_if_not_exists(): void
    {
        $this->assertDatabaseCount('settings', 0);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'title'       => 'Перший заголовок',
                'description' => 'Перший опис',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Налаштування збережено');

        $this->assertDatabaseCount('settings', 1);
        $this->assertDatabaseHas('settings', ['title' => 'Перший заголовок']);
    }

    public function test_update_overwrites_existing_settings(): void
    {
        Setting::factory()->create([
            'title'       => 'Старий заголовок',
            'description' => 'Старий опис',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'title'       => 'Новий заголовок',
                'description' => 'Новий опис',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Налаштування збережено');

        $this->assertDatabaseCount('settings', 1);
        $this->assertDatabaseHas('settings', ['title' => 'Новий заголовок']);
        $this->assertDatabaseMissing('settings', ['title' => 'Старий заголовок']);
    }

    public function test_update_with_favicon_saves_file(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'title'       => 'Заголовок',
                'description' => 'Опис',
                'favicon'     => UploadedFile::fake()->image('favicon.png'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Налаштування збережено');

        $settings = Setting::first();
        $this->assertNotNull($settings->favicon);
        Storage::disk('public')->assertExists($settings->favicon);
    }

    public function test_update_without_favicon_keeps_existing_favicon(): void
    {
        Storage::fake('public');

        Setting::factory()->create([
            'title'   => 'Заголовок',
            'favicon' => 'images/favicons/old-favicon.png',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'title'       => 'Оновлений заголовок',
                'description' => 'Опис',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Налаштування збережено');

        $this->assertDatabaseHas('settings', [
            'favicon' => 'images/favicons/old-favicon.png',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_settings(): void
    {
        $this->get(route('admin.settings.index'))
            ->assertRedirect(route('login'));
    }

}
