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

    public function findLowStockProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.stockQuantity <= p.minStockLevel')
            ->andWhere('p.isActive = true')
            ->orderBy('p.stockQuantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOutOfStockProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.stockQuantity = 0')
            ->andWhere('p.isActive = true')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchForPOS(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :query OR p.barcode LIKE :query')
            ->andWhere('p.isActive = true')
            ->andWhere('p.stockQuantity > 0')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
