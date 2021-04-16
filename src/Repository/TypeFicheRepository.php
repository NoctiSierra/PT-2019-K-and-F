<?php

namespace App\Repository;

use App\Entity\TypeFiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TypeFiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeFiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeFiche[]    findAll()
 * @method TypeFiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeFicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeFiche::class);
    }

    // /**
    //  * @return TypeFiche[] Returns an array of TypeFiche objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeFiche
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
