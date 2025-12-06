<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Find products with low stock (less than 10)
     */
    public function findLowStock(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stockQuantity < 10')
            ->andWhere('p.stockQuantity > 0')
            ->orderBy('p.stockQuantity', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products out of stock
     */
    public function findOutOfStock(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stockQuantity = 0')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search products by name or reference
     */
    public function search(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :query OR p.reference LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by category
     */
    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find expiring products (expiry date within next 30 days)
     */
    public function findExpiringSoon(): array
    {
        $now = new \DateTime();
        $inThirtyDays = (new \DateTime())->modify('+30 days');

        return $this->createQueryBuilder('p')
            ->andWhere('p.expiryDate BETWEEN :now AND :future')
            ->setParameter('now', $now)
            ->setParameter('future', $inThirtyDays)
            ->orderBy('p.expiryDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}