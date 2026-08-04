<?php
namespace Tests\Unit\Traits;

use App\Traits\DateFormats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DateFormatsTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::create(2026, 7, 20, 12, 0, 0)
        );

        $this->model = new class extends Model {
            use DateFormats;

            protected $guarded = [];

            protected $casts = [
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
            ];
        };

        $this->model->created_at = Carbon::create(2026, 7, 19, 12, 0, 0);
        $this->model->updated_at = Carbon::create(2026, 7, 18, 12, 0, 0);
    }


    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }


    public function test_created_at_for_humans_accessor(): void
    {
        $this->assertNotEmpty(
            $this->model->created_at_for_humans
        );
    }


    public function test_updated_at_for_humans_accessor(): void
    {
        $this->assertNotEmpty(
            $this->model->updated_at_for_humans
        );
    }


    public function test_created_at_for_humans_carbon_accessor(): void
    {
        $this->assertNotEmpty(
            $this->model->created_at_for_humans_carbon
        );
    }


    public function test_updated_at_for_humans_carbon_accessor(): void
    {
        $this->assertNotEmpty(
            $this->model->updated_at_for_humans_carbon
        );
    }


    public function test_created_at_formatter_accessor(): void
    {
        $this->assertMatchesRegularExpression(
            '/\d{1,2}\s.+\s\d{2}/u',
            $this->model->created_at_formatter
        );
    }


    public function test_updated_at_formatter_accessor(): void
    {
        $this->assertMatchesRegularExpression(
            '/\d{1,2}\s.+\s\d{2}/u',
            $this->model->updated_at_formatter
        );
    }

}
