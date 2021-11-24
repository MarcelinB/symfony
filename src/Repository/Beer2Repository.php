<?php

namespace App\Repository;

use App\Entity\Beer2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Beer2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beer2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beer2[]    findAll()
 * @method Beer2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Beer2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beer2::class);
    }

    // /**
    //  * @return Beer2[] Returns an array of Beer2 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Beer2
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
