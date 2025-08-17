<?php
declare(strict_types=1);

namespace App\Collection\Item;

use App\Entity\Item;
use App\Enum\ItemType;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

abstract class AbstractItemCollection implements ItemCollectionInterface
{
    public function __construct(
        protected readonly ItemRepository $repository,
        protected readonly ItemType $type
    ) {
    }

    /**
     * @param Item $item
     * @return void
     */
    public function add(Item $item): void
    {
        if ($item->getType() !== $this->type) {
            throw new InvalidArgumentException(
                sprintf('%s collection accepts only %s items', $this->type->name, strtolower($this->type->name))
            );
        }
        $this->repository->save($item);
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        $item = $this->repository->findOneBy([
            'name' => $name,
            'type' => $this->type,
        ]);

        if ($item !== null) {
            $this->repository->remove($item);
        }
    }

    /**
     * @param array $filters
     * @return Collection
     */
    public function list(array $filters = []): Collection
    {
        $criteria = array_merge(['type' => $this->type], $filters);
        return new ArrayCollection($this->repository->findByFilters($criteria));
    }
}