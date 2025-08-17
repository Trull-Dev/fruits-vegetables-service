<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Collection\Item\FruitCollection;
use App\DTO\Item as ItemDTO;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/vegetables')]
final class VegetableController extends AbstractController
{
    public function __construct(
        private readonly ItemService $itemService,
        private readonly FruitCollection $collection
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(
        #[MapQueryParameter] ?string $name = null,
        #[MapQueryParameter] ?float $min = null,
        #[MapQueryParameter] ?float $max = null,
    ): JsonResponse {
        $query = array_filter([
            'name' => $name,
            'min' => $min,
            'max' => $max,
        ], fn($value) => $value !== null);

        return $this->json(
            $this->itemService->list($query, $this->collection)
        );
    }

    #[Route('', methods: ['POST'])]
    public function add(#[MapRequestPayload] ItemDTO $dto): JsonResponse
    {
        $this->itemService->add($dto, $this->collection);

        return $this->json(null, Response::HTTP_CREATED);
    }

    #[Route('/{name}', methods: ['DELETE'])]
    public function remove(string $name): JsonResponse
    {
        $this->itemService->remove($name, $this->collection);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}