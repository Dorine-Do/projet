<?php

namespace App\Repository;

use App\Entity\Main\LinkSessionModule;
use App\Entity\Main\LinkSessionStudent;
use App\Entity\Main\Session;
use App\Entity\Main\LinkInstructorSessionModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function add(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getInstructorSessions($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DISTINCT session.id, session.name
            FROM link_instructor_session_module 
            JOIN session ON link_instructor_session_module.session_id = session.id 
            JOIN link_session_module ON link_session_module.session_id = session.id
           WHERE link_instructor_session_module.instructor_id = ?
             AND link_session_module.start_date <= NOW() 
             AND link_session_module.end_date >= NOW()
        ';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery([$id]);

        return $resultSet->fetchAllAssociative();
    }

    public function getInstructorSessionsInYear($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DISTINCT session.id, session.name
            FROM link_instructor_session_module 
            JOIN session ON link_instructor_session_module.session_id = session.id 
            JOIN link_session_module ON link_session_module.session_id = session.id
            WHERE link_instructor_session_module.instructor_id = ?
            AND link_session_module.start_date <= DATE( NOW() + INTERVAL 6 MONTH)
            AND link_session_module.end_date >= DATE( NOW() - INTERVAL 6 MONTH)
            ORDER BY session.name ASC
        ';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery([$id]);

        return $resultSet->fetchAllAssociative();
    }

    public function findSessionByString($str)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.name LIKE :str' )
            ->setParameter('str', '%'.$str.'%')
            ->getQuery()
            ->getResult();
    }

    public function findSessionByQcm($qcmId)
    {
        return $this->createQueryBuilder('s')
            ->select("s.id as sId, s.name")
            ->innerJoin('s.linksSessionStudent', 'lss')
            ->innerJoin('lss.student', 'st')
            ->innerJoin('st.qcmInstances', 'qi')
            ->innerJoin('qi.qcm', 'q')
            ->where('q.id = :qcmId')
            ->andWhere('q.isOfficial = true')
            ->setParameter('qcmId', $qcmId)
            ->getQuery()
            ->getResult();
    }

    public function findModuleByResult($resultId)
    {
        return $this->createQueryBuilder('m')
            ->select("m.id as mId, m.name")
            ->innerJoin('m.qcm', 'qcm')
            ->innerJoin('qcm.qcmInstance', 'qi')
            ->innerJoin('qi.result', 'r')
            ->where('r.id = :rId')
            ->andWhere('r.qcmInstanceId = true')
            ->setParameter('resultId', $resultId)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Session[] Returns an array of Session objects
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

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
