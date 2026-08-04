<?php
namespace Tests\Unit\Helpers;

use Tests\TestCase;

class PluralizeResultsTest extends TestCase
{
    public function test_returns_singular_for_one_result(): void
    {
        $this->assertSame(
            'збіг',
            pluralize_results(1)
        );
    }


    public function test_returns_plural_for_zero_results(): void
    {
        $this->assertSame(
            'збігів',
            pluralize_results(0)
        );
    }


    public function test_returns_plural_for_multiple_results(): void
    {
        $this->assertSame(
            'збігів',
            pluralize_results(5)
        );
    }

}
