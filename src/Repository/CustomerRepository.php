<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * Find active customers
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search customers by name, email or phone
     */
    public function search(string $query): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name LIKE :query OR c.email LIKE :query OR c.phone LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find customers by city
     */
    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.city = :city')
            ->setParameter('city', $city)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}