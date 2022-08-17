<?php

namespace App\Repository;

use App\Entity\Main\LinkSessionModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkSessionModule>
 *
 * @method LinkSessionModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkSessionModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkSessionModule[]    findAll()
 * @method LinkSessionModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkSessionModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkSessionModule::class);
    }

    public function add(LinkSessionModule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LinkSessionModule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LinkSessionModule[] Returns an array of LinkSessionModule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LinkSessionModule
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
