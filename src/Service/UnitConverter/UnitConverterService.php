<?php
declare(strict_types=1);

namespace App\Service\UnitConverter;

use App\Enum\UnitType;

final class UnitConverterService implements UnitConverterInterface
{
    /**
     * @param float $amount
     * @param UnitType $unit
     * @return int
     */
    public function toGrams(float $amount, UnitType $unit): int
    {
        return match ($unit) {
            UnitType::Kilogram => (int)round($amount * 1000.0),
            UnitType::Gram => (int)round($amount),
        };
    }

    /**
     * @param int $grams
     * @param UnitType $targetUnit
     * @return float
     */
    public function fromGrams(int $grams, UnitType $targetUnit): float
    {
        return match ($targetUnit) {
            UnitType::Kilogram => $grams / 1000.0,
            UnitType::Gram => (float)$grams,
        };
    }
}