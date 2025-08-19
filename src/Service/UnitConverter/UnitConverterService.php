<?php
declare(strict_types=1);

namespace App\Service\UnitConverter;

use App\Enum\UnitType;

final class UnitConverterService implements UnitConverterInterface
{
    /**
     * @param float $quantity
     * @param UnitType $unit
     * @return float
     */
    public function toGrams(float $quantity, UnitType $unit): float
    {
        return match ($unit) {
            UnitType::Kilogram => (int)round($quantity * 1000.0),
            UnitType::Gram => (int)round($quantity),
        };
    }

    /**
     * @param float $grams
     * @param UnitType $targetUnit
     * @return float
     */
    public function fromGrams(float $grams, UnitType $targetUnit): float
    {
        return match ($targetUnit) {
            UnitType::Kilogram => $grams / 1000.0,
            UnitType::Gram => (float)$grams,
        };
    }
}