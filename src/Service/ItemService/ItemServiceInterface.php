<?php
declare(strict_types=1);

namespace App\Service\ItemService;

use App\Collection\Item\ItemCollectionInterface;
use App\DTO\Item as ItemDTO;
use Symfony\Component\HttpFoundation\Request;

interface ItemServiceInterface
{
    public function add(ItemDTO $dto, ItemCollectionInterface $collection): void;
    public function remove(string $name, ItemCollectionInterface $collection): void;
    public function list(Request $request, ItemCollectionInterface $collection): array;
}