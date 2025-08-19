<?php
declare(strict_types=1);

namespace App\DTO;

use App\Enum\ItemType;
use App\Enum\UnitType;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Item
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name can\'t be blank')]
        #[Assert\Length(max: 255)]
        #[SerializedName('name')]
        public string $name,

        #[Assert\NotNull]
        #[Assert\Positive(message: 'Quantity can\'t be negative')]
        #[SerializedName('quantity')]
        public float $quantity,

        #[Assert\NotNull]
        #[Assert\Type(UnitType::class)]
        #[SerializedName('unit')]
        public UnitType $unit,

        #[Assert\NotNull]
        #[Assert\Type(ItemType::class)]
        #[SerializedName('type')]
        public ItemType $type,
    ) {
    }
}