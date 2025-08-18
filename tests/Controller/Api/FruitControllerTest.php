<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Collection\Item\FruitCollection;
use App\Controller\Api\FruitController;
use App\DTO\Item;
use App\Enum\ItemType;
use App\Enum\UnitType;
use App\Service\ItemService\ItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class FruitControllerTest extends WebTestCase
{
    private FruitController $controller;
    private ItemServiceInterface $itemService;
    private FruitCollection $collection;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->itemService = $this->createMock(ItemServiceInterface::class);
        $this->collection = $this->createMock(FruitCollection::class);

        $container = static::getContainer();
        $container->set(ItemServiceInterface::class, $this->itemService);
        $container->set(FruitCollection::class, $this->collection);

        $this->controller = new FruitController($this->itemService, $this->collection);
        $this->controller->setContainer($container);
    }

    public function testList(): void
    {
        $expectedResponse = [
            ['name' => 'banana', 'amount' => 150.0, 'unit' => UnitType::Gram->value, 'type' => ItemType::Fruit->value]
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
        $dto = new Item(
            name: 'banana',
            amount: 150.0,
            unit: UnitType::Gram,
            type: ItemType::Fruit
        );

        $this->itemService->expects($this->once())
            ->method('add')
            ->with($dto, $this->collection);

        $response = $this->controller->add($dto);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testRemove(): void
    {
        $this->itemService->expects($this->once())
            ->method('remove')
            ->with('banana', $this->collection);

        $response = $this->controller->remove('banana');
        $this->assertEquals(204, $response->getStatusCode());
    }
}