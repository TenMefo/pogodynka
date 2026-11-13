<?php

namespace App\Repository;

use App\Entity\Measurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Location;

/**
 * @extends ServiceEntityRepository<Measurement>
 */
class MeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

//    /**
//     * @return Measurement[] Returns an array of Measurement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Measurement
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByLocation(Location $location): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.location = :location')
            ->setParameter('location', $location)

            ->andWhere('m.date >= :tomorrow')
            ->setParameter('tomorrow', new \DateTime('tomorrow'), Types::DATETIME_MUTABLE)

            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
