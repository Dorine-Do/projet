<?php

namespace App\Repository;

use App\Entity\Main\LinkInstructorSessionModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkInstructorSessionModule>
 *
 * @method LinkInstructorSessionModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkInstructorSessionModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkInstructorSessionModule[]    findAll()
 * @method LinkInstructorSessionModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkInstructorSessionModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkInstructorSessionModule::class);
    }

    public function add(LinkInstructorSessionModule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LinkInstructorSessionModule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LinkInstructorSessionModule[] Returns an array of LinkInstructorSessionModule objects
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

//    public function findOneBySomeField($value): ?LinkInstructorSessionModule
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
