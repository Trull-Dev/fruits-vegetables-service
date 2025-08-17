<?php
declare(strict_types=1);

namespace App\DTO;

use App\Enum\ItemType;
use App\Enum\UnitType;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Item
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name can\'t be blank')]
        #[Assert\Length(max: 255)]
        public string $name,

        #[Assert\NotNull]
        #[Assert\Positive(message: 'Amount can\'t be negative')]
        public float $amount,

        #[Assert\NotNull]
        #[Assert\Type(UnitType::class)]
        public UnitType $unit,

        #[Assert\NotNull]
        #[Assert\Type(ItemType::class)]
        public ItemType $type,
    ) {
    }
}