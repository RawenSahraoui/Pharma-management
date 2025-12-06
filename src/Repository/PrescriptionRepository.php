<?php

namespace App\Repository;

use App\Entity\Prescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prescription>
 */
class PrescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.prescriptionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCustomer(int $customerId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.customer = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('p.prescriptionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDoctor(int $doctorId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.doctor = :doctorId')
            ->setParameter('doctorId', $doctorId)
            ->orderBy('p.prescriptionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}