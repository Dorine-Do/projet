<?php

namespace App\Repository;

use App\Entity\Main\LinkSessionStudent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkSessionStudent>
 *
 * @method LinkSessionStudent|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkSessionStudent|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkSessionStudent[]    findAll()
 * @method LinkSessionStudent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkSessionStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkSessionStudent::class);
    }

    public function add(LinkSessionStudent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LinkSessionStudent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LinkSessionStudent[] Returns an array of LinkSessionStudent objects
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

//    public function findOneBySomeField($value): ?LinkSessionStudent
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
