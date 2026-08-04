<?php
namespace Tests\Unit\Models\Film;

use App\DateTimeObjects\DatePickerValue;
use App\Models\Film;
use Tests\TestCase;

class FilmDisplayDateTest extends TestCase
{
    public function test_display_date_uses_datepicker_when_it_exists(): void
    {
        $film = new Film();

        $film->setRawAttributes([
            'datepicker' => '2025-07-20',
        ]);

        $film->datepicker = new DatePickerValue('2025-07-20');

        $this->assertSame(
            '20.07.2025',
            $film->display_date
        );
    }


    public function test_display_date_falls_back_to_created_at_formatter_when_datepicker_is_empty(): void
    {
        $film = new Film();

        $film->setRawAttributes([
            'datepicker' => null,
        ]);

        $film->created_at = now();

        $this->assertSame(
            $film->createdAtFormatter,
            $film->display_date
        );
    }


    public function test_display_date_falls_back_when_datepicker_is_empty_string(): void
    {
        $film = new Film();

        $film->setRawAttributes([
            'datepicker' => '',
        ]);

        $film->created_at = now();

        $this->assertSame(
            $film->createdAtFormatter,
            $film->display_date
        );
    }

}
