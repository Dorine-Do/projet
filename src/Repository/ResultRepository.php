<?php

namespace App\Repository;

use App\Entity\Main\Module;
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
     * @return Result[] Returns an array of Result objects
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

    /**
     * @return Result[] Returns an array of Result objects
     */
    public function getOfficialQcmsSuccessByModule($userId, $titleModule): array
    {
        return $this->createQueryBuilder('r')
            ->select("r.id, r.score")
            ->innerJoin('r.qcmInstance', 'qi')
            ->innerJoin('qi.student', 's')
            ->innerJoin('s.linksSessionStudent', 'lss')
            ->innerJoin('qi.qcm', 'q')
            ->innerJoin('q.module', 'm')
            ->where('s.id = :userId')
            ->andWhere('q.isOfficial = true')
            ->andWhere('lss.isEnabled = true')
            ->andWhere('r.score >= 50')
            ->andWhere('m.title LIKE :titleModule')
            ->setParameter('userId', $userId)
            ->setParameter('titleModule', '%'.$titleModule.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Result[] Returns an array of Student objects
     */
    public function resultMaxScore($id): array
    {
        return $this->createQueryBuilder('r')
            ->select('MAX(r.score) as score, r.id as id, r.level')
            ->innerJoin('r.qcmInstance', 'qi')
            ->innerJoin('qi.qcm', 'q')
            ->innerJoin('q.module', 'm')
            ->innerJoin('qi.student', 's')
            ->where('s.id = :id')
            ->andWhere('q.isOfficial = 1')
            ->groupBy('m.id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Result[] Returns an array of Student objects
     */
    public function resultWithQcmOfficialByModule($idStudent, $idSession): array
    {
        return $this->createQueryBuilder('r')
            ->select('
                r.id as resultId, 
                r.level, 
                r.score,
                s.id as studentId,
                se.id as sessionId, 
                m.id as moduleId, 
                m.title, 
                q.id as qId, 
                qi.id as qiId,
                lsm.startDate,
                lsm.endDate'
            )
            ->innerJoin('r.qcmInstance', 'qi')
            ->innerJoin('qi.qcm', 'q')
            ->innerJoin('q.module', 'm')
            ->innerJoin('m.linksSessionModule', 'lsm')
            ->innerJoin('qi.student', 's')
            ->innerJoin('s.linksSessionStudent', 'lss')
            ->innerJoin('lss.session', 'se')
            ->where('s.id = :idStudent')
            ->andwhere('lsm.session = :idSession')
            ->andWhere('q.isOfficial = 1')
            ->andWhere('q.isPublic = 1')
            ->andWhere('r.isFirstTry = 1')
            ->andWhere('lss.isEnabled = 1')
            ->andWhere('lsm.endDate <= CURRENT_DATE()')
            ->setParameter('idStudent', $idStudent)
            ->setParameter('idSession', $idSession)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Module[] Returns an array of Student objects
     */
    public function isOfficialQcmLevel($idStudent,$idSession): array
    {
        return $this->createQueryBuilder('r')
            ->select(
                "q.id as qcmId,
                 q.title as qcmTitle,
                 qi.id as qcmInstanceId, 
                 r.id as resultID, 
                 m.id as moduleId, 
                 m.title as moduleTitle,
                 r.level,
                 DATE_FORMAT(lsm.startDate,'%Y-%m-%d') as startDat,
                 DATE_FORMAT(lsm.endDate,'%Y-%m-%d') as endDate
                 ")
            ->innerJoin('r.qcmInstance', 'qi')
            ->innerJoin('qi.qcm', 'q')
            ->innerJoin('q.module', 'm')
            ->innerJoin('m.linksSessionModule', 'lsm')
            ->innerJoin('qi.student', 's')
            ->innerJoin('s.linksSessionStudent', 'lss')
            ->innerJoin('lss.session', 'se')
            ->where('s.id = :idStudent')
            ->andwhere('lsm.session = :idSession')
            ->andWhere('q.isOfficial = 1')
            ->andWhere('q.isPublic = 1')
            ->andWhere('r.isFirstTry = 1')
            ->andWhere('lss.isEnabled = 1')
            ->andWhere('lsm.endDate <= CURRENT_DATE()')
            ->setParameter('idStudent', $idStudent)
            ->setParameter('idSession', $idSession)
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
