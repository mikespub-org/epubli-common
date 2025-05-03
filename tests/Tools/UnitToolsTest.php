<?php

namespace Epubli\Common\Tools;

use PHPUnit\Framework\TestCase;

class UnitToolsTest extends TestCase
{
    protected float $epsilon = 0.00001;

    /**
     * @param float $mm value in mm
     * @param float $pt value in pt
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testMmToPt($mm, $pt)
    {
        $this->assertEqualsWithDelta($pt, UnitTools::mmToPt($mm), $this->epsilon, '');
    }

    /**
     * @param float $mm value in mm
     * @param float $pt value in pt
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testPtToMm($mm, $pt)
    {
        $this->assertEqualsWithDelta($mm, UnitTools::ptToMm($pt), $this->epsilon, '');
    }

    public static function provideData()
    {
        return [
            [3, 8.503937],
            [1.058333, 3],
            [14.816667, 42],
            [42, 119.055118],
        ];
    }
}
