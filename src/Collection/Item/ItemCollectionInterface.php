<?php
declare(strict_types=1);

namespace App\Collection\Item;

use App\Entity\Item;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;

interface ItemCollectionInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function add(Item $item): void;

    public function remove(string $name): void;

    /**
     * @param array<string, mixed> $filters
     * @return Collection<int, Item>
     */
    public function list(array $filters = []): Collection;
}