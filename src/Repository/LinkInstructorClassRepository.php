<?php

namespace App\Repository;

use App\Entity\LinkInstructorClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkInstructorClass>
 *
 * @method LinkInstructorClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkInstructorClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkInstructorClass[]    findAll()
 * @method LinkInstructorClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkInstructorClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkInstructorClass::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(LinkInstructorClass $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(LinkInstructorClass $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

//    /**
//     * @return LinkInstructorClass[] Returns an array of LinkInstructorClass objects
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

//    public function findOneBySomeField($value): ?LinkInstructorClass
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
