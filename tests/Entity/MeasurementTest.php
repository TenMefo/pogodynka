<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MeasurementTest extends TestCase
{
    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(string $celsius, string $expectedFahrenheit): void
    {
        $measurement = (new Measurement())->setCelsius($celsius);

        self::assertSame($expectedFahrenheit, $measurement->getFahrenheit());
    }

    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', '32'],
            ['-100', '-148'],
            ['100', '212'],
            ['0.5', '32.9'],
            ['-0.5', '31.1'],
            ['-40', '-40'],
            ['37', '98.6'],
            ['-17.75', '0.05'],
            ['20.25', '68.45'],
            ['12.34', '54.212'],
        ];
    }
}
