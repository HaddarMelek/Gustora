<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;

class PublicProductController extends AbstractController
{
    #[Route('/products', name: 'app_products_public')]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $productRepository->findAll();
        
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            2 
        );

        return $this->render('product/public_listing.html.twig', [
            'pagination' => $pagination 
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