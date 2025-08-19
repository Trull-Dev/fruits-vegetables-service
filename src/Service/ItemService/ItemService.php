<?php
declare(strict_types=1);

namespace App\Service\ItemService;

use App\Collection\Item\ItemCollectionInterface;
use App\DTO\Item as ItemDTO;
use App\Entity\Item;
use App\Enum\UnitType;
use App\Service\UnitConverter\UnitConverterInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ValueError;

final readonly class ItemService implements ItemServiceInterface
{
    public function __construct(
        private UnitConverterInterface $converter,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @param ItemDTO $dto
     * @param ItemCollectionInterface $collection
     * @return void
     */
    public function add(ItemDTO $dto, ItemCollectionInterface $collection): void
    {
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            throw new InvalidArgumentException($violations->get(0)->getMessage());
        }

        $grams = $this->converter->toGrams($dto->quantity, $dto->unit);
        $collection->add(new Item($dto->name, $dto->type, $grams));
    }

    /**
     * @param string $name
     * @param ItemCollectionInterface $collection
     * @return void
     */
    public function remove(string $name, ItemCollectionInterface $collection): void
    {
        $collection->remove($name);
    }

    /**
     * @param Request $request
     * @param ItemCollectionInterface $collection
     * @return array
     */
    public function list(Request $request, ItemCollectionInterface $collection): array
    {
        try {
            $unit = $request->query->get('unit') !== null
                ? UnitType::from($request->query->get('unit'))
                : UnitType::Gram;
        } catch (ValueError $e) {
            throw new InvalidArgumentException('Invalid unit type provided');
        }

        return array_map(
            fn(Item $item) => [
                'name' => $item->getName(),
                'type' => $item->getType()->value,
                'quantity' => $this->converter->fromGrams($item->getQuantityInGrams(), $unit),
                'unit' => $unit->value,
            ],
            $collection->list($this->createFilters($request->query->all()))->toArray()
        );

    }

    /**
     * @param array $query
     * @return array
     */
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