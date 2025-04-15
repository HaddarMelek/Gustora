<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;

class PublicProductController extends AbstractController
{
    #[Route('/products', name: 'app_products_public')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/public_listing.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    #[Route('/products/{id}', name: 'app_public_product_show', methods: ['GET'])]
public function show(Product $product): Response
{
    return $this->render('product/public_show.html.twig', [
        'product' => $product,
    ]);
}
}