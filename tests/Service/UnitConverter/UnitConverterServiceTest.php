<?php
declare(strict_types=1);

namespace App\Tests\Service\UnitConverter;

use App\Enum\UnitType;
use App\Service\UnitConverter\UnitConverterService;
use PHPUnit\Framework\TestCase;

class UnitConverterServiceTest extends TestCase
{
    private UnitConverterService $converter;

    protected function setUp(): void
    {
        $this->converter = new UnitConverterService();
    }

    /**
     * @dataProvider toGramsProvider
     */
    public function testToGrams(float $quantity, UnitType $unit, float $expectedGrams): void
    {
        $result = $this->converter->toGrams($quantity, $unit);
        $this->assertEquals($expectedGrams, $result);
    }

    /**
     * @dataProvider fromGramsProvider
     */
    public function testFromGrams(float $grams, UnitType $targetUnit, float $expectedQuantity): void
    {
        $result = $this->converter->fromGrams($grams, $targetUnit);
        $this->assertEquals($expectedQuantity, $result);
    }

    /**
     * @return array<string, array{float, UnitType, float}>
     */
    public function toGramsProvider(): array
    {
        return [
            'gram to gram' => [100.0, UnitType::Gram, 100.0],
            'gram to gram with decimal' => [100.4, UnitType::Gram, 100.0],
            'gram to gram with rounding up' => [100.6, UnitType::Gram, 101.0],
            'kilogram to gram' => [1.0, UnitType::Kilogram, 1000.0],
            'kilogram to gram with decimal' => [1.5, UnitType::Kilogram, 1500.0],
            'kilogram to gram with small decimal' => [0.001, UnitType::Kilogram, 1.0],
            'zero grams' => [0.0, UnitType::Gram, 0.0],
            'zero kilograms' => [0.0, UnitType::Kilogram, 0.0],
            'large number of grams' => [999999.0, UnitType::Gram, 999999.0],
        ];
    }

    /**
     * @return array<string, array{float, UnitType, float}>
     */
    public function fromGramsProvider(): array
    {
        return [
            'gram to gram' => [100.0, UnitType::Gram, 100.0],
            'gram to kilogram' => [1000.0, UnitType::Kilogram, 1.0],
            'gram to kilogram with decimal' => [1500.0, UnitType::Kilogram, 1.5],
            'small quantity to kilogram' => [1.0, UnitType::Kilogram, 0.001],
            'zero to gram' => [0.0, UnitType::Gram, 0.0],
            'zero to kilogram' => [0.0, UnitType::Kilogram, 0.0],
            'large number to gram' => [999999.0, UnitType::Gram, 999999.0],
        ];
    }

    public function testRoundTripConversion(): void
    {
        $originalQuantity = 1.5;
        $grams = $this->converter->toGrams($originalQuantity, UnitType::Kilogram);
        $result = $this->converter->fromGrams($grams, UnitType::Kilogram);

        $this->assertEquals($originalQuantity, $result);
    }

    public function testPrecisionHandling(): void
    {
        $smallQuantity = 0.0001;
        $grams = $this->converter->toGrams($smallQuantity, UnitType::Kilogram);
        $this->assertEquals(0, $grams, 'Very small quantity should be rounded to 0 grams');

        $largeQuantity = 9999.999;
        $grams = $this->converter->toGrams($largeQuantity, UnitType::Kilogram);
        $backToKg = $this->converter->fromGrams($grams, UnitType::Kilogram);
        $this->assertEqualsWithDelta($largeQuantity, $backToKg, 0.001, 'Large quantity should maintain precision');
    }

    public function testNegativeValues(): void
    {
        $negativeGrams = -100.0;
        $resultKg = $this->converter->fromGrams($negativeGrams, UnitType::Kilogram);
        $this->assertEquals(-0.1, $resultKg, 'Should handle negative values correctly');

        $negativeKg = -1.5;
        $resultGrams = $this->converter->toGrams($negativeKg, UnitType::Kilogram);
        $this->assertEquals(-1500, $resultGrams, 'Should handle negative values correctly');
    }

    public function testEdgeCases(): void
    {
        $maxFloat = PHP_FLOAT_MAX;
        $resultGrams = $this->converter->toGrams($maxFloat, UnitType::Gram);
        $this->assertIsFloat($resultGrams, 'Should handle maximum float values');

        $minFloat = PHP_FLOAT_MIN;
        $resultKg = $this->converter->fromGrams($minFloat, UnitType::Kilogram);
        $this->assertIsFloat($resultKg, 'Should handle minimum float values');
    }
}