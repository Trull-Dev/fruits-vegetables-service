<?php
declare(strict_types=1);

namespace App\Collection\Item;

use App\Enum\ItemType;
use App\Repository\ItemRepository;

final class FruitCollection extends AbstractItemCollection
{
    public function __construct(ItemRepository $repository)
    {
        parent::__construct($repository, ItemType::Fruit);
    }
}