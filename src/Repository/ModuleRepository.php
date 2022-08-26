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

    public function getAccomplishedModules( int $student_id ): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT module.id, module.weeks, module.title, module.created_at, module.updated_at
            FROM user
            INNER JOIN qcm_instance ON qcm_instance.student_id = user.id
            INNER JOIN qcm ON qcm_instance.qcm_id = qcm.id
            INNER JOIN result ON result.qcm_instance_id = qcm_instance.id
            INNER JOIN module ON qcm.module_id = module.id
            INNER JOIN link_session_module ON link_session_module.module_id = module.id
            WHERE result.score >= 50 AND link_session_module.end_date <= now() AND qcm.is_official = true AND user.id = :student_id
        ';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['student_id' => $student_id]);

        return $resultSet->fetchAllAssociative();
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

    //     ;
    // }

    public function getModules( $instructor )
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->join(LinkInstructorSessionModule::class, 'lism')
            ->where('lism.instructor = :instructor' )
            ->andWhere( 'lism.module = m.id' )
            /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
            ->setParameter('instructor', 1 );
    }
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
