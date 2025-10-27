<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\Contract\OrderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Persist an Order in memory (and optionally flush it to database).
     *
     * @param Order $entity
     *
     * @implements RepositoryInterface<Order>
     */
    public function save(object $entity, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);

        if ($flush) {
            $em->flush();
        }
    }

    public function remove(object $entity, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        if ($flush) {
            $em->flush();
        }
    }

    public function customerHasArticle(Customer $customer, int $articleId): bool
    {
        return (bool) $this->createQueryBuilder('o')
            ->select('1')
            ->innerJoin('o.orderItems', 'oi')
            ->where('o.customer = :customer')
            ->andWhere('oi.itemType = :type')
            ->andWhere('oi.itemId = :id')
            ->setParameter('customer', $customer)
            ->setParameter('type', 'article')
            ->setParameter('id', $articleId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function customerHasSubscription(Customer $customer): bool
    {
        return (bool) $this->createQueryBuilder('o')
            ->select('1')
            ->innerJoin('o.orderItems', 'oi')
            ->where('o.customer = :customer')
            ->andWhere('oi.itemType = :type')
            ->setParameter('customer', $customer)
            ->setParameter('type', 'subscription')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Order[] Returns an array of Order objects
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

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
