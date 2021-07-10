<?php

namespace App\Repository;

use App\Entity\ProductCommand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCommand[]    findAll()
 * @method ProductCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCommand::class);
    }

    // /**
    //  * @return ProductCommand[] Returns an array of ProductCommand objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductCommand
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
