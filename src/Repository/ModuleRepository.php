<?php

namespace App\Repository;

use App\Entity\Main\LinkInstructorSessionModule;
use App\Entity\Main\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function add(Module $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Module $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getRetryableModules( int $student_id ): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT module.id as moduleId, MAX(result.score) as maxModuleOfficialScore FROM module
            LEFT JOIN qcm ON module.id = qcm.module_id
            LEFT JOIN qcm_instance ON qcm_instance.qcm_id = qcm.id
            LEFT JOIN user ON qcm_instance.student_id = user.id
            LEFT JOIN link_session_student ON link_session_student.student_id = user.id
            LEFT JOIN result ON result.qcm_instance_id = qcm_instance.id
            WHERE user.id = 11 AND link_session_student.is_enabled = true AND qcm.is_official = true
            GROUP BY module.id
            HAVING maxModuleOfficialScore IS NULL OR maxModuleOfficialScore < 50            
        ';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

        public function getModuleSessions($id)
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->join(LinkInstructorSessionModule::class, 'lism')
            ->where('lism.session = :session' )
            ->andWhere( 'lism.module = m.id' )
            ->setParameter('session', $id )
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Module[] Returns an array of Module objects
     */
    public function getModulesByModuleBaseName($userId, $titleModule): array
    {
        return $this->createQueryBuilder('m')
            ->select("DISTINCT m.id as mId, m.title")
            ->innerJoin('m.qcms', 'q')
            ->innerJoin('q.qcmInstances', 'qi')
            ->innerJoin('qi.student', 's')
            ->innerJoin('s.linksSessionStudent', 'lss')
            ->where('s.id = :userId')
            ->andWhere('lss.isEnabled = true')
            ->andWhere('m.title LIKE :titleModule')
            ->setParameter('userId', $userId)
            ->setParameter('titleModule', '%'.$titleModule.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Module[] Returns an array of Module objects
     */
    public function findAllModulesByBaseName( $titleModule ): array
    {
        return $this->createQueryBuilder('m')
            ->select("m")
            ->andWhere('m.title LIKE :titleModule')
            ->setParameter('titleModule', '%'.$titleModule.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Module[] Returns an array of Student objects
     */
    public function moduleMaxScore($id): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.title, m.id, r.level')
            ->innerJoin('m.qcms', 'q')
            ->innerJoin('q.qcmInstances', 'qi')
            ->innerJoin('qi.result', 'r')
            ->innerJoin('qi.student', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }


    //    /**
    //  * @throws ORMException
    //  * @throws OptimisticLockException
    //  */
    // public function getModule($instructorId)
    // {
    //     $entityManager = $this->getEntityManager();

    //     return  $entityManager->createQuery(
    //         'SELECT m
    //         FROM App\Entity\Module m
    //         INNER JOIN App\Entity\LinkInstructorSessionModule lism
    //         WHERE lism.instructor = :instructor
    //         AND lism.module = m.id
    //        '
    //     )->setParameter(':instructor',$instructorId)
    //     ;
    // }

//    /**
//     * @return Module[] Returns an array of Module objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Module
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
