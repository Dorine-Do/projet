<?php

namespace App\Repository;

use App\Entity\Main\Module;
use App\Entity\Main\Qcm;
use App\Entity\Main\QcmInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Qcm>
 *
 * @method Qcm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Qcm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Qcm[]    findAll()
 * @method Qcm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QcmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Qcm::class);
    }

    public function add(Qcm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Qcm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getTestQuestion()
    {
        $qcmBdd = $this->getEntityManager();
        return $qcmBdd->createQuery('
        SELECT q
        FROM App\Entity\Main\Qcm q      
        ')
            ->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
        public function testQcm()
        {
            $qcmBdd = $this->getEntityManager();
            return $qcmBdd->createQuery('
            SELECT q.id, q.title
            FROM App\Entity\Main\Qcm q
        ')
            ->getResult();
        }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getQcmByInstructor()
    {
        $qcmBdd = $this->getEntityManager();
        return $qcmBdd->createQuery('
        SELECT q.title, q.difficulty
        FROM App\Entity\Main\Qcm q
        ')
            ->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findByQcmId(int $qcmId): ?Qcm
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q, qu
            FROM App\Entity\Main\Qcm q
            INNER JOIN q.questions qu
            WHERE  q.id = :id
        ')
        ->setParameter('id', $qcmId);

        return $query->getOneOrNullResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findByQcmIdAuthor(int $id_author): ?Qcm
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q, qu
            FROM App\Entity\Main\Qcm q
            INNER JOIN q.questions qu
            WHERE q.author = :id_author
        ')
        ->setParameter('id_author', $id_author);

        return $query->getOneOrNullResult();

    }

    /**
    * @throws ORMException
    * @throws OptimisticLockException
    */
    public function test(int $id_author,int  $qcmId): ?Qcm
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q, qu
            FROM App\Entity\Main\Qcm q
            INNER JOIN q.questions qu
            WHERE q.author = :id_author
            AND  q.id = :id
           ')
        ->setParameter('id', $qcmId)
        ->setParameter('id_author', $id_author);

        return $query->getResult();
        
    }

    public function getQcmModules($id)
    {
        return $this->createQueryBuilder('q')
            ->select('q')
            ->innerJoin('q.module', 'm')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Qcm[] Returns an array of Qcm objects
     */
    public function getQcmsByStudentAndModule($moduleId, $studentId): array
    {
        return $this->createQueryBuilder('q')
        ->select('qi.id, r.level, m.title, q.difficulty, q.isOfficial, r.submittedAt, r.id as resultId')
        ->innerJoin('q.qcmInstances', 'qi')
        ->innerJoin('qi.student', 's')
        ->innerJoin('qi.result', 'r')
        ->innerJoin('q.module', 'm')
        ->where('s.id = :student_id')
        ->andWhere('m.id = :module_id')
        ->setParameter('student_id', $studentId)
        ->setParameter('module_id', $moduleId)
        ->getQuery()
        ->getResult()
        ;
    }

    /**
     * @return Qcm[] Returns an array of Qcm objects
     */
    public function getQcmDistributedByUser($userId, $moduleId) :array
    {
        return $this->createQueryBuilder('q')
            ->select('DISTINCT q')
            ->innerJoin(QcmInstance::class, 'qcmi')
            ->where('q.isOfficial = 1')
            ->orWhere('q.isOfficial = 0')
            ->andWhere('q.isEnabled = 1')
            ->andWhere('qcmi.distributedBy = :userId')
            ->andWhere('q.module = :moduleId')
            ->setParameter('userId', $userId)
            ->setParameter('moduleId', $moduleId)
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Qcm[] Returns an array of Qcm objects
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

//    public function findOneBySomeField($value): ?Qcm
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
