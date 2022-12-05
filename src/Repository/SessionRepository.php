<?php

namespace App\Repository;

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
        return $this->createQueryBuilder('s')
            ->select('s')
            ->join(LinkInstructorSessionModule::class, 'lism')
            ->where('lism.instructor = :instructor' )
            ->andWhere( 'lism.session = s.id' )
            ->join(LinkSessionStudent::class, 'lss')
            ->andWhere('lss.isEnabled = true')
            ->setParameter('instructor', $id )
            ->getQuery()
            ->getResult();
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
