<?php

namespace App\Repository;

use App\Entity\Main\QcmInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QcmInstance>
 *
 * @method QcmInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method QcmInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method QcmInstance[]    findAll()
 * @method QcmInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QcmInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QcmInstance::class);
    }

    public function add(QcmInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QcmInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return QcmInstance[] Returns an array of Student objects
     */
    public function AllQcmInstanceWithoutResult(): array
    {

        $qcmInstanceBdd = $this->getEntityManager();
        return $qcmInstanceBdd->createQuery('
            SELECT qi
            FROM App\Entity\Main\QcmInstance qi
            WHERE qi.id NOT IN (
                SELECT IDENTITY (r.qcmInstance)
                FROM App\Entity\Main\Result r
            )
        ')
            ->getResult();
        }

    /**
     * @return QcmInstance[] Returns an array of Student objects
     */
    public function getQcmInstancesByStudent($id): array
    {
        return $this->createQueryBuilder('qi')
            ->innerJoin('qi.student', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }


//    /**
//     * @return QcmInstance[] Returns an array of QcmInstance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?QcmInstance
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
