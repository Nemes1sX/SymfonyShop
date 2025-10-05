<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

class HomeController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function home(string $sortParam): Response
    {
        $products = $this->productRepository->findBySortParam($sortParam);

        return $this->render('index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
