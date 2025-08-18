<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Collection\Item\VegetableCollection;
use App\Controller\Api\VegetableController;
use App\DTO\Item;
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
            ['name' => 'carrot', 'amount' => 100.0, 'unit' => UnitType::Gram->value, 'type' => ItemType::Vegetable->value]
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
            name: 'carrot',
            amount: 100.0,
            unit: UnitType::Gram,
            type: ItemType::Vegetable
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
            ->with('carrot', $this->collection);

        $response = $this->controller->remove('carrot');
        $this->assertEquals(204, $response->getStatusCode());
    }
}