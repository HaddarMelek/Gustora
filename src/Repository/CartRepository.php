<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findByUser(User $user): ?Cart
    {
        return $this->findOneBy(['user' => $user]);
    }

    public function save(Cart $cart, bool $flush = true): void
    {
        $this->getEntityManager()->persist($cart);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cart $cart, bool $flush = true): void
    {
        $this->getEntityManager()->remove($cart);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCartWithItems(User $user): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->leftJoin('c.items', 'i')
            ->leftJoin('i.product', 'p')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}