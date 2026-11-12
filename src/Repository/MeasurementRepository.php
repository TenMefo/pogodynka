<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Measurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Measurement>
 */
class MeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

    /**
     * Zwraca pomiary danej miejscowości z datą późniejszą niż dziś.
     *
     * @return list<Measurement>
     */
    public function findByLocation(Location $location): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.location = :location')
            ->setParameter('location', $location)
            ->andWhere('m.date > :today')
            ->setParameter('today', date('Y-m-d'))
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
