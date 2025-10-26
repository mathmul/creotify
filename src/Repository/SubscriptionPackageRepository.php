<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SubscriptionPackage;
use App\Repository\Contract\SubscriptionPackageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriptionPackage>
 */
class SubscriptionPackageRepository extends ServiceEntityRepository implements SubscriptionPackageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionPackage::class);
    }

    /**
     * Persist an SubscriptionPackage in memory (and optionally flush it to database).
     *
     * @param SubscriptionPackage $entity
     *
     * @implements RepositoryInterface<SubscriptionPackage>
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
    //     * @return SubscriptionPackage[] Returns an array of SubscriptionPackage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SubscriptionPackage
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
