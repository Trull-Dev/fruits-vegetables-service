<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FruitController extends AbstractController
{
    #[Route('/fruit')]
    public function index(): Response
    {
        return $this->render('fruit/index.html.twig');
    }
}
