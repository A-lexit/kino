<?php
namespace Tests\Unit\Models\Film;

use App\Models\Actor;
use App\Models\Age;
use App\Models\Caption;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Composer;
use App\Models\Country;
use App\Models\Director;
use App\Models\Duration;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Producer;
use App\Models\Quality;
use App\Models\Rating;
use App\Models\Season;
use App\Models\Selection;
use App\Models\State;
use App\Models\Status;
use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

class FilmRelationsTest extends TestCase
{
    private Film $film;

    protected function setUp(): void
    {
        parent::setUp();

        $this->film = new Film();
    }


    public function test_state_relation(): void
    {
        $relation = $this->film->state();

        $this->assertInstanceOf(HasOne::class, $relation);
        $this->assertInstanceOf(State::class, $relation->getRelated());
    }


    public function test_comments_relation(): void
    {
        $relation = $this->film->comments();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(Comment::class, $relation->getRelated());
    }


    public function test_duration_relation(): void
    {
        $relation = $this->film->duration();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Duration::class, $relation->getRelated());
    }


    public function test_quality_relation(): void
    {
        $relation = $this->film->quality();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Quality::class, $relation->getRelated());
    }


    public function test_season_relation(): void
    {
        $relation = $this->film->season();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Season::class, $relation->getRelated());
    }


    public function test_category_relation(): void
    {
        $relation = $this->film->category();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Category::class, $relation->getRelated());
    }


    public function test_year_relation(): void
    {
        $relation = $this->film->year();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Year::class, $relation->getRelated());
    }


    public function test_rating_relation(): void
    {
        $relation = $this->film->rating();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Rating::class, $relation->getRelated());
    }


    public function test_status_relation(): void
    {
        $relation = $this->film->status();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Status::class, $relation->getRelated());
    }


    public function test_age_relation(): void
    {
        $relation = $this->film->age();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Age::class, $relation->getRelated());
    }


    public function test_user_relation(): void
    {
        $relation = $this->film->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }


    public function test_directors_relation(): void
    {
        $relation = $this->film->directors();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Director::class, $relation->getRelated());
    }


    public function test_composers_relation(): void
    {
        $relation = $this->film->composers();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Composer::class, $relation->getRelated());
    }


    public function test_companies_relation(): void
    {
        $relation = $this->film->companies();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Company::class, $relation->getRelated());
    }


    public function test_actors_relation(): void
    {
        $relation = $this->film->actors();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Actor::class, $relation->getRelated());
    }


    public function test_producers_relation(): void
    {
        $relation = $this->film->producers();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Producer::class, $relation->getRelated());
    }


    public function test_genres_relation(): void
    {
        $relation = $this->film->genres();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Genre::class, $relation->getRelated());
    }


    public function test_languages_relation(): void
    {
        $relation = $this->film->languages();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Language::class, $relation->getRelated());
    }


    public function test_countries_relation(): void
    {
        $relation = $this->film->countries();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Country::class, $relation->getRelated());
    }


    public function test_captions_relation(): void
    {
        $relation = $this->film->captions();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Caption::class, $relation->getRelated());
    }


    public function test_selections_relation(): void
    {
        $relation = $this->film->selections();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Selection::class, $relation->getRelated());
    }

}
