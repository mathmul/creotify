<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrderItem;
use App\Repository\Contract\OrderItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository implements OrderItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * Persist an OrderItem in memory (and optionally flush it to database).
     *
     * @param OrderItem $entity
     *
     * @implements RepositoryInterface<OrderItem>
     */
    public function save(object $entity, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);

        if ($flush) {
            $em->flush();
        }
    }

    //    /**
    //     * @return OrderItem[] Returns an array of OrderItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OrderItem
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
