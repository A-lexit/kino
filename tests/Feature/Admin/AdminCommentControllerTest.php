<?php
namespace Tests\Feature\Admin;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_index_displays_comments(): void
    {
        Comment::factory()->count(3)->create();

        $this->get(route('admin.comments.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.comments.index')
            ->assertViewHas('comments');
    }

    public function test_toggle_method_switches_status_and_redirects(): void
    {
        $comment = Comment::factory()->create(['status' => 0]);

        // Актуальний варіант: toggle() тепер AJAX-ендпоінт (дивись CommentController@toggle,
        // там теж лишений закоментований старий redirect-варіант поруч із новим) —
        // повертає JSON { success, status, message }, а не redirect.
        $this->get(route('admin.comments.toggle', $comment->id))
            ->assertStatus(200)
            ->assertJson(['success' => true, 'status' => 1]);

        $this->assertEquals(1, $comment->fresh()->status);

        // Перевіряємо зворотне перемикання (0 -> 1 -> 0) — та сама логіка,
        // просто ще раз викликаємо той самий ендпоінт
        $this->get(route('admin.comments.toggle', $comment->id))
            ->assertStatus(200)
            ->assertJson(['success' => true, 'status' => 0]);

        $this->assertEquals(0, $comment->fresh()->status);
    }

    public function test_destroy_method_deletes_comment(): void
    {
        $comment = Comment::factory()->create();

        // destroy() у контролері підтримує і звичайний, і AJAX-запит
        // (перевіряє $request->ajax()) — цей тест іде класичним шляхом,
        // тому редірект тут лишається правильним, нічого міняти не треба
        $this->delete(route('admin.comments.destroy', $comment->id))
            ->assertRedirect(route('admin.comments.index'))
            ->assertSessionHas('success', 'Коментар видалено');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

}
