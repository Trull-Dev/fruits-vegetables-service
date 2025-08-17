<?php
declare(strict_types=1);

namespace App\Tests\Collection\Item;

use App\Collection\Item\AbstractItemCollection;
use App\Entity\Item;
use App\Enum\ItemType;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class VegetableItemCollectionTest extends TestCase
{
    private ItemRepository $repository;
    private AbstractItemCollection $collection;
    private ItemType $type;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ItemRepository::class);
        $this->type = ItemType::Vegetable;
        $this->collection = $this->getMockForAbstractClass(
            AbstractItemCollection::class,
            [$this->repository, $this->type]
        );
    }

    public function testAddItemWithCorrectType(): void
    {
        $item = new Item('carrot', ItemType::Vegetable, 100);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($item);

        $this->collection->add($item);
    }

    public function testAddItemWithWrongTypeThrowsException(): void
    {
        $item = new Item('apple', ItemType::Fruit, 100);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vegetable collection accepts only vegetable items');

        $this->collection->add($item);
    }

    public function testRemoveExistingItem(): void
    {
        $item = new Item('carrot', ItemType::Vegetable, 100);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'carrot', 'type' => $this->type])
            ->willReturn($item);

        $this->repository->expects($this->once())
            ->method('remove')
            ->with($item);

        $this->collection->remove('carrot');
    }

    public function testRemoveNonExistingItem(): void
    {
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'carrot', 'type' => $this->type])
            ->willReturn(null);

        $this->repository->expects($this->never())
            ->method('remove');

        $this->collection->remove('carrot');
    }

    public function testList(): void
    {
        $items = [
            new Item('carrot', ItemType::Vegetable, 100),
            new Item('salad', ItemType::Vegetable, 150)
        ];

        $this->repository->expects($this->once())
            ->method('findByFilters')
            ->with(['type' => $this->type])
            ->willReturn($items);

        $result = $this->collection->list();

        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($items, $result->toArray());
    }

    public function testListWithFilters(): void
    {
        $filters = ['name' => 'carrot'];
        $items = [new Item('carrot', ItemType::Vegetable, 100)];

        $this->repository->expects($this->once())
            ->method('findByFilters')
            ->with(['type' => $this->type, 'name' => 'carrot'])
            ->willReturn($items);

        $result = $this->collection->list($filters);

        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame($items, $result->toArray());
    }
}