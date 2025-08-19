<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Collection\Item\FruitCollection;
use App\DTO\ItemRequest;
use App\Enum\ItemType;
use App\Service\ItemService\ItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/fruits')]
final class FruitController extends AbstractController
{
    public function __construct(
        private readonly ItemServiceInterface $itemService,
        private readonly FruitCollection $collection
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse {
        return $this->json(
            $this->itemService->list($request, $this->collection)
        );
    }

    #[Route('', methods: ['POST'])]
    public function add(#[MapRequestPayload] ItemRequest $itemRequest): JsonResponse
    {
        foreach ($itemRequest->items as $item) {
            if ($item->type === ItemType::Fruit) {
                $this->itemService->add($item, $this->collection);
            }
        }

        return $this->json(null, Response::HTTP_CREATED);
    }


    #[Route('/{name}', methods: ['DELETE'])]
    public function remove(string $name): JsonResponse
    {
        $this->itemService->remove($name, $this->collection);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}