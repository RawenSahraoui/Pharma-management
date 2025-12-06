<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * Find sales by date range
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.saleDate BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('s.saleDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total sales amount for a period
     */
    public function getTotalSalesAmount(\DateTimeInterface $startDate, \DateTimeInterface $endDate): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.totalAmount)')
            ->andWhere('s.saleDate BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Get sales count for a period
     */
    public function getSalesCount(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.saleDate BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find sales by customer
     */
    public function findByCustomer(int $customerId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.customer = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('s.saleDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get recent sales (last N sales)
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.saleDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}