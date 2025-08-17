<?php
declare(strict_types=1);

namespace App\Service\UnitConverter;

use App\Enum\UnitType;

final class UnitConverter implements UnitConverterInterface
{
    public function toGrams(float $amount, UnitType $unit): int
    {
        return match ($unit) {
            UnitType::Kilogram => (int)round($amount * 1000.0),
            UnitType::Gram => (int)round($amount),
        };
    }

    public function fromGrams(int $grams, UnitType $targetUnit): float
    {
        return match ($targetUnit) {
            UnitType::Kilogram => $grams / 1000.0,
            UnitType::Gram => (float)$grams,
        };
    }
}