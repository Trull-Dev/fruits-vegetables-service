<?php
declare(strict_types=1);

namespace App\Service\UnitConverter;

use App\Enum\UnitType;

interface UnitConverterInterface
{
    /** @return int grams */
    public function toGrams(float $amount, UnitType $unit): int;

    /** @return float amount in requested unit */
    public function fromGrams(int $grams, UnitType $targetUnit): float;
}