<?php

namespace App\Repository;

use App\Entity\OrderLines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderLines>
 */
class OrderLinesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLines::class);
    }

    //    /**
    //     * @return OrderLines[] Returns an array of OrderLines objects
    //     */
        public function findByExampleField(int $id): array
        {
            return $this->createQueryBuilder('o')
                ->andWhere('o.order_id = :id')
                ->setParameter('id', $id)
                ->orderBy('o.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
      }

    //    public function findOneBySomeField($value): ?OrderLines
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
