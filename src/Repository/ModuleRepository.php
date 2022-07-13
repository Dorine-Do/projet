<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Module $entity, bool $flush = false): void
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
    public function remove(Module $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getAccomplishedModules( int $student_id ): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT module.id, module.number_of_weeks, module.title, module.badges, module.created_at, module.updated_at
            FROM student
            INNER JOIN result ON result.student_id = student.id
            INNER JOIN qcm_instance ON result.qcm_instance_id = qcm_instance.id
            INNER JOIN qcm ON qcm_instance.qcm_id = qcm.id
            INNER JOIN link_module_qcm ON link_module_qcm.qcm_id = qcm.id
            INNER JOIN module ON link_module_qcm.module_id = module.id
            INNER JOIN link_session_module ON link_session_module.module_id = module.id
            WHERE result.total_score >= 50 AND link_session_module.end_date <= now() AND qcm.is_official = true AND student.id = :student_id
        ';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['student_id' => $student_id]);

        return $resultSet->fetchAllAssociative();
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
