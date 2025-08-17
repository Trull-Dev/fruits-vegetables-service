<?php
declare(strict_types=1);

namespace App\Service;

use App\Collection\Item\ItemCollectionInterface;
use App\Entity\Item;
use App\DTO\Item as ItemDTO;
use App\Service\UnitConverter\UnitConverterInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ItemService
{
    public function __construct(
        private UnitConverterInterface $converter,
        private ValidatorInterface $validator
    ) {
    }

    public function add(ItemDTO $dto, ItemCollectionInterface $collection): void
    {
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            throw new InvalidArgumentException($violations->get(0)->getMessage());
        }

        $grams = $this->converter->toGrams($dto->amount, $dto->unit);
        $collection->add(new Item($dto->name, $dto->type, $grams));
    }

    public function remove(string $name, ItemCollectionInterface $collection): void
    {
        $collection->remove($name);
    }

    public function list(array $query, ItemCollectionInterface $collection): array
    {
        return array_map(
            fn(Item $item) => [
                'name' => $item->getName(),
                'type' => $item->getType()->value,
                'amount' => $item->getAmountInGrams()
            ],
            $collection->list($this->createFilters($query))->toArray()
        );
    }

    private function createFilters(array $query): array
    {
        $filters = isset($query['name']) ? ['name' => $query['name']] : [];

        if (isset($query['min'])) {
            $filters['minGrams'] = (float)$query['min'];
        }

        if (isset($query['max'])) {
            $filters['maxGrams'] = (float)$query['max'];
        }

        return $filters;
    }
}