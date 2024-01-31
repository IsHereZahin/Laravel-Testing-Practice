<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_convert_usd_to_bdt_successful(): void
    {
        // $this->assertEquals( 109.60, (new CurrencyService())->convert(1, 'usd', 'bdt')); //Shortcut

        $result = (new CurrencyService())->convert(1, 'usd', 'bdt');

        $this->assertEquals('109.60', $result);
    }

    public function test_convert_usd_to_rub_successful(): void
    {
        // $this->assertEquals( 87.96, (new CurrencyService())->convert(1, 'usd', 'rub')); //Shortcut

        $this->assertEquals('87.96', (new CurrencyService())->convert(1, 'usd', 'rub'));
    }

    public function test_convert_bdt_to_usd_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot convert from BDT to USD');

        (new CurrencyService())->convert(1, 'bdt', 'usd');
    }
}
