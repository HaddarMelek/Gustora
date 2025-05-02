<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;

class PublicProductController extends AbstractController
{
    #[Route('/user', name: 'app_products_public')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $category = $request->query->get('category');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
    
        $queryBuilder = $productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c');
    
        if ($category) {
            $queryBuilder->andWhere('c.id = :category')
                ->setParameter('category', $category);
        }
    
        if ($minPrice) {
            $queryBuilder->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }
    
        if ($maxPrice) {
            $queryBuilder->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }
    
        $categories = $categoryRepository->findAll();
    
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            4
        );
    
        return $this->render('product/public_listing.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'selectedCategory' => $category,
            'selectedMinPrice' => $minPrice,
            'selectedMaxPrice' => $maxPrice
        ]);
    }
    
    #[Route('/user/{id}', name: 'app_public_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/public_show.html.twig', [
            'product' => $product,
        ]);
    }

}