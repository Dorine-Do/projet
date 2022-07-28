<?php

namespace App\Repository;

use App\Entity\Qcm;
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
        FROM App\Entity\Qcm q
       
        
       
       
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
        FROM App\Entity\Qcm q
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
        FROM App\Entity\Qcm q
       
       
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
               FROM App\Entity\Qcm q
               INNER JOIN q.questions qu
               WHERE q.id = :id'
           )->setParameter('id', $qcmId);
   
           return $query->getOneOrNullResult();
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
