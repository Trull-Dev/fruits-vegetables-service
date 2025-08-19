<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', enumType: ItemType::class)]
    private ItemType $type;

    #[ORM\Column(name: 'quantity_in_grams', type: 'float')]
    private float $quantityInGrams;

    public function __construct(
        string $name,
        ItemType $type,
        float $quantityInGrams,
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->quantityInGrams = $quantityInGrams;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ItemType
    {
        return $this->type;
    }

    public function setType(ItemType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getQuantityInGrams(): float
    {
        return $this->quantityInGrams;
    }

    public function setQuantityInGrams(float $quantityInGrams): self
    {
        $this->quantityInGrams = $quantityInGrams;
        return $this;
    }
}