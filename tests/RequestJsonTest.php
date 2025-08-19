<?php
declare(strict_types=1);

namespace App\Tests;

use App\Collection\Item\FruitCollection;
use App\Collection\Item\VegetableCollection;
use App\Controller\Api\FruitController;
use App\Controller\Api\VegetableController;
use App\DTO\Item;
use App\DTO\ItemRequest;
use App\Enum\ItemType;
use App\Enum\UnitType;
use App\Service\ItemService\ItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group functionality-test
 */
final class RequestJsonTest extends KernelTestCase

{
    private ItemServiceInterface $itemService;
    private FruitCollection $fruitCollection;
    private VegetableCollection $vegetableCollection;
    private FruitController $fruitController;
    private VegetableController $vegetableController;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->itemService = $container->get(ItemServiceInterface::class);
        $this->fruitCollection = $container->get(FruitCollection::class);
        $this->vegetableCollection = $container->get(VegetableCollection::class);

        $this->fruitController = new FruitController($this->itemService, $this->fruitCollection);
        $this->vegetableController = new VegetableController($this->itemService, $this->vegetableCollection);

        $this->fruitController->setContainer($container);
        $this->vegetableController->setContainer($container);
    }

    public function testProcessRequestJson(): void
    {
        $jsonContent = file_get_contents(__DIR__ . '/../request.json');
        $data = json_decode($jsonContent, true);

        $items = array_map(function (array $itemData) {
            return new Item(
                name: $itemData['name'],
                quantity: (float)$itemData['quantity'],
                unit: UnitType::from($itemData['unit']),
                type: ItemType::from($itemData['type'])
            );
        }, $data);

        $itemRequest = new ItemRequest(items: $items);

        $fruitResponse = $this->fruitController->add($itemRequest);
        $this->assertEquals(201, $fruitResponse->getStatusCode());

        $vegetableResponse = $this->vegetableController->add($itemRequest);
        $this->assertEquals(201, $vegetableResponse->getStatusCode());

        $fruitList = $this->fruitController->list(new Request());
        $vegetableList = $this->vegetableController->list(new Request());

        $this->assertNotEmpty(json_decode($fruitList->getContent(), true));
        $this->assertNotEmpty(json_decode($vegetableList->getContent(), true));
    }
}