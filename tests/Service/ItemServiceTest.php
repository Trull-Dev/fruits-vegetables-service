<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Collection\Item\ItemCollectionInterface;
use App\DTO\Item as ItemDTO;
use App\Entity\Item;
use App\Enum\ItemType;
use App\Enum\UnitType;
use App\Service\ItemService\ItemService;
use App\Service\UnitConverter\UnitConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

class ItemServiceTest extends TestCase
{
    private UnitConverterInterface $converter;
    private ValidatorInterface $validator;
    private ItemService $service;
    private ItemCollectionInterface $collection;

    protected function setUp(): void
    {
        $this->converter = $this->createMock(UnitConverterInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->collection = $this->createMock(ItemCollectionInterface::class);
        $this->service = new ItemService($this->converter, $this->validator);
    }

    public function testAddValidItem(): void
    {
        $dto = new ItemDTO(
            name: 'apple',
            quantity: 100,
            unit: UnitType::Gram,
            type: ItemType::Fruit
        );


        $this->validator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn(new ConstraintViolationList());

        $this->converter->expects($this->once())
            ->method('toGrams')
            ->with(100, UnitType::Gram)
            ->willReturn(100.0);

        $this->collection->expects($this->once())
            ->method('add')
            ->with($this->callback(function(Item $item) {
                return $item->getName() === 'apple'
                    && $item->getType() === ItemType::Fruit
                    && $item->getQuantityInGrams() === 100.0;
            }));

        $this->service->add($dto, $this->collection);
    }

    public function testAddInvalidItemConstructorValidation(): void
    {
        $this->expectException(TypeError::class);

        new ItemDTO(
            name: 4,
            type: ItemType::Fruit,
            quantity: 100,
            unit: UnitType::Gram
        );
    }

    public function testAddItemWithValidatorViolations(): void
    {
        $dto = new ItemDTO(
            name: 'apple',
            quantity: -100,
            unit: UnitType::Gram,
            type: ItemType::Fruit
        );

        $violation = $this->createMock(ConstraintViolation::class);
        $violation->method('getMessage')->willReturn('Quantity must be positive');

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn(new ConstraintViolationList([$violation]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        $this->service->add($dto, $this->collection);
    }

    public function testRemoveItem(): void
    {
        $this->collection->expects($this->once())
            ->method('remove')
            ->with('apple');

        $this->service->remove('apple', $this->collection);
    }

    public function testListItems(): void
    {
        $request = new Request(['unit' => 'g']);
        $items = [new Item('apple', ItemType::Fruit, 100)];

        $this->collection->expects($this->once())
            ->method('list')
            ->with([])
            ->willReturn(new ArrayCollection($items));

        $this->converter->expects($this->once())
            ->method('fromGrams')
            ->with(100, UnitType::Gram)
            ->willReturn(100.0);

        $result = $this->service->list($request, $this->collection);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'name' => 'apple',
            'type' => 'fruit',
            'quantity' => 100.0,
            'unit' => 'g'
        ], $result[0]);
    }

    public function testListItemsWithInvalidUnit(): void
    {
        $request = new Request(['unit' => 'invalid']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid unit type provided');

        $this->service->list($request, $this->collection);
    }
}