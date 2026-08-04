<?php
namespace Tests\Unit\Models\User;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class UserRelationsTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
    }


    public function test_films_relation(): void
    {
        $relation = $this->user->films();

        $this->assertInstanceOf(
            HasMany::class,
            $relation
        );

        $this->assertInstanceOf(
            Film::class,
            $relation->getRelated()
        );
    }


    public function test_comments_relation(): void
    {
        $relation = $this->user->comments();

        $this->assertInstanceOf(
            HasMany::class,
            $relation
        );

        $this->assertInstanceOf(
            Comment::class,
            $relation->getRelated()
        );
    }


    public function test_films_relation_uses_correct_keys(): void
    {
        $relation = $this->user->films();

        $this->assertSame(
            'author_id',
            $relation->getForeignKeyName()
        );

        $this->assertSame(
            'id',
            $relation->getLocalKeyName()
        );
    }


    public function test_comments_relation_uses_correct_keys(): void
    {
        $relation = $this->user->comments();

        $this->assertSame(
            'user_id',
            $relation->getForeignKeyName()
        );

        $this->assertSame(
            'id',
            $relation->getLocalKeyName()
        );
    }

}
