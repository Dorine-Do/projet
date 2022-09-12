<?php

namespace App\Repository;

use App\Entity\Main\Result;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Result>
 *
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    public function add(Result $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Result $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Result[] Returns an array of Student objects
     */
    public function maxScoreByModuleAndSession($sessionId, $moduleId): array
    {
        return $this->createQueryBuilder('r')
            ->select('s.id, s.firstName, s.lastName , MAX(r.score) AS max_score, r.level')
            ->innerJoin('r.qcmInstance', 'qi')
            ->innerJoin('qi.student', 's')
            ->innerJoin('s.linksSessionStudent', 'lss')
            ->innerJoin('qi.qcm', 'q')
            ->innerJoin('q.module', 'm')
            ->where('lss.session = :session_id')
            ->andWhere('m.id = :module_id')
            ->groupBy('s.id')
            ->setParameter('session_id', $sessionId)
            ->setParameter('module_id', $moduleId)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Result[] Returns an array of Result objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Result
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
