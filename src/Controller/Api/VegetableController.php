<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Collection\Item\VegetableCollection;
use App\DTO\Item as ItemDTO;
use App\Service\ItemService\ItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/vegetables')]
final class VegetableController extends AbstractController
{
    public function __construct(
        private readonly ItemServiceInterface $itemService,
        private readonly VegetableCollection $collection
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(
        Request $request,
        #[MapQueryParameter] ?string $name = null,
        #[MapQueryParameter] ?string $unit = null,
        #[MapQueryParameter] ?float $min = null,
        #[MapQueryParameter] ?float $max = null,
    ): JsonResponse {
        return $this->json(
            $this->itemService->list($request, $this->collection)
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