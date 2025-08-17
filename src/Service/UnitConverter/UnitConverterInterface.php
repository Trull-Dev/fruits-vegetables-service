<?php
declare(strict_types=1);

namespace App\Service\UnitConverter;

use App\Enum\UnitType;

interface UnitConverterInterface
{
    /**
     * @param float $amount
     * @param UnitType $unit
     * @return float grams
     */
    public function toGrams(float $amount, UnitType $unit): float;

    /**
     * @param float $grams
     * @param UnitType $targetUnit
     * @return float
     */
    public function fromGrams(float $grams, UnitType $targetUnit): float;
}