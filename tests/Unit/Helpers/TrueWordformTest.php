<?php
namespace Tests\Unit\Helpers;

use Tests\TestCase;

class TrueWordformTest extends TestCase
{
    public function test_returns_zero_form(): void
    {
        $this->assertSame(
            'коментарів немає',
            true_wordform(
                0,
                'коментарів немає',
                'коментар',
                'коментаря',
                'коментарів'
            )
        );
    }


    public function test_returns_one_form(): void
    {
        $this->assertSame(
            'коментар',
            true_wordform(
                1,
                'коментарів немає',
                'коментар',
                'коментаря',
                'коментарів'
            )
        );
    }


    public function test_returns_two_form(): void
    {
        $this->assertSame(
            'коментаря',
            true_wordform(
                2,
                'коментарів немає',
                'коментар',
                'коментаря',
                'коментарів'
            )
        );
    }


    public function test_returns_five_form(): void
    {
        $this->assertSame(
            'коментарів',
            true_wordform(
                5,
                'коментарів немає',
                'коментар',
                'коментаря',
                'коментарів'
            )
        );
    }


    public function test_handles_numbers_between_11_and_19(): void
    {
        foreach ([11, 12, 13, 14, 15, 19] as $number) {

            $this->assertSame(
                'коментарів',
                true_wordform(
                    $number,
                    'коментарів немає',
                    'коментар',
                    'коментаря',
                    'коментарів'
                )
            );
        }
    }


    public function test_handles_numbers_ending_with_one(): void
    {
        foreach ([21, 31, 101, 121] as $number) {

            $this->assertSame(
                'коментар',
                true_wordform(
                    $number,
                    'коментарів немає',
                    'коментар',
                    'коментаря',
                    'коментарів'
                )
            );
        }
    }


    public function test_handles_numbers_ending_with_two_three_four(): void
    {
        foreach ([22, 23, 24, 32, 103] as $number) {

            $this->assertSame(
                'коментаря',
                true_wordform(
                    $number,
                    'коментарів немає',
                    'коментар',
                    'коментаря',
                    'коментарів'
                )
            );
        }
    }


    public function test_handles_large_numbers(): void
    {
        $this->assertSame(
            'коментарів',
            true_wordform(
                1005,
                'коментарів немає',
                'коментар',
                'коментаря',
                'коментарів'
            )
        );
    }

}
