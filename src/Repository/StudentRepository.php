<?php

namespace App\Repository;

use App\Entity\Main\Module;
use App\Entity\Main\Result;
use App\Entity\Main\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function add(Student $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Student $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Student[] Returns an array of Student objects
     */
    public function findByEnabled(): array
    {
        $studentBdd = $this->getEntityManager();
        return $studentBdd->createQuery('
        SELECT DISTINCT s
        FROM App\Entity\Main\Student s
        INNER JOIN App\Entity\Main\LinkSessionStudent lss
        WITH lss.student = s.id
        WHERE lss.isEnabled = true
        ')
            ->getResult();
    }

    /**
     * @return Student[] Returns an array of Student objects
     */
    public function AllStudentByQcmInstance($id, $entityManager): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.qcmInstances', 'qi')
            ->innerJoin('qi.resutl', 'r')
            ->where('qi.id = :id')
            ->andWhere('qi.result === null')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Student[] Returns an array of Student objects
     */
    public function isOfficialQcmLevel($id): array
    {
        return $this->createQueryBuilder('s')
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
            ->innerJoin('s.qcmInstances', 'qi')
            ->innerJoin('qi.result', 'r')
            ->innerJoin('qi.qcm', 'q')
            ->innerJoin('q.module', 'm')
            ->innerJoin('m.linksSessionModule', 'lsm')
            ->where('s.id = :id')
            ->andWhere('q.isOfficial = true')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    /*
     * SELECT module.id, module.name ,MAX(result.score) FROM `user`
        INNER JOIN qcm_instance ON qcm_instance.student_id = user.id
        INNER JOIN result ON result.qcm_instance_id = qcm_instance.id
        INNER JOIN qcm ON qcm.id = qcm_instance.qcm_id
        INNER JOIN module ON module.id = qcm.module_id
        WHERE user.id = 11
        GROUP BY module.id;
     */

//    /**
//     * @return Student[] Returns an array of Student objects
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

//    public function findOneBySomeField($value): ?Student
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
