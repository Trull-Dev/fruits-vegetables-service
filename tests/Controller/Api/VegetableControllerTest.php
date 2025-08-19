<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Collection\Item\VegetableCollection;
use App\Controller\Api\VegetableController;
use App\DTO\Item;
use App\DTO\ItemRequest;
use App\Enum\ItemType;
use App\Enum\UnitType;
use App\Service\ItemService\ItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class VegetableControllerTest extends WebTestCase
{
    private VegetableController $controller;
    private ItemServiceInterface $itemService;
    private VegetableCollection $collection;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->itemService = $this->createMock(ItemServiceInterface::class);
        $this->collection = $this->createMock(VegetableCollection::class);

        $container = static::getContainer();
        $container->set(ItemServiceInterface::class, $this->itemService);
        $container->set(VegetableCollection::class, $this->collection);

        $this->controller = new VegetableController($this->itemService, $this->collection);
        $this->controller->setContainer($container);
    }

    public function testList(): void
    {
        $expectedResponse = [
            ['name' => 'carrot', 'quantity' => 100.0, 'unit' => UnitType::Gram->value, 'type' => ItemType::Vegetable->value]
        ];

        $this->itemService->expects($this->once())
            ->method('list')
            ->willReturn($expectedResponse);

        $response = $this->controller->list(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedResponse),
            $response->getContent()
        );
    }

    public function testAdd(): void
    {
        $items = [
            new Item(
                name: 'carrot',
                quantity: 150.0,
                unit: UnitType::Gram,
                type: ItemType::Vegetable
            ),
            new Item(
                name: 'salad',
                quantity: 150.0,
                unit: UnitType::Gram,
                type: ItemType::Vegetable
            ),
        ];

        $itemRequest = new ItemRequest(items: $items);

        $this->itemService
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnCallback(function($item, $collection) use ($items) {
                static $callNumber = 0;
                $this->assertSame($items[$callNumber], $item);
                $this->assertSame($this->collection, $collection);
                $callNumber++;
            });

        $response = $this->controller->add($itemRequest);
        $this->assertEquals(201, $response->getStatusCode());
    }


    public function testRemove(): void
    {
        $this->itemService->expects($this->once())
            ->method('remove')
            ->with('carrot', $this->collection);

        $response = $this->controller->remove('carrot');
        $this->assertEquals(204, $response->getStatusCode());
    }
}