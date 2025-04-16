<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

public function findByFilters($minPrice = null, $maxPrice = null, $categoryId = null)
{
    $qb = $this->createQueryBuilder('p')  ->leftJoin('p.category', 'c');

    if ($minPrice !== null) {
        $qb->andWhere('p.price >= :minPrice')->setParameter('minPrice', $minPrice);
    }

    if ($maxPrice !== null) {
        $qb->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', $maxPrice);
    }

    if ($categoryId !== null && $categoryId !== '') {
        $qb->andWhere('c.id = :categoryId')->setParameter('categoryId', $categoryId);
    }

    return $qb->getQuery()->getResult();
}

}

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
// Compare this snippet from src/Entity/Product.php:    