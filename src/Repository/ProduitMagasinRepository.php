<?php

namespace App\Repository;

use App\Entity\ProduitMagasin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProduitMagasin|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitMagasin|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitMagasin[]    findAll()
 * @method ProduitMagasin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitMagasinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitMagasin::class);
    }

    // /**
    //  * @return ProduitMagasin[] Returns an array of ProduitMagasin objects
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
    public function findOneBySomeField($value): ?ProduitMagasin
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
