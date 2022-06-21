<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /* MEMO
        /*$questionBdd = $this->getEntityManager();
        return $questionBdd->createQuery('
        SELECT q
        FROM App\Entity\Question q
        INNER JOIN App\Entity\Module m
        WITH m.id = q.module_id
        INNER JOIN App\Entity\LinkInstructorModule lim
        WITH m.id = lim.module_id
        INNER JOIN App\Entity\Instructor i
        WITH i.id = lim.instructor_id
        WHERE q.id > :id_question
        AND i.id > :id_instructor
     ')
            ->setParameter('id_question', $question_id)
            ->setParameter('id_instructor', $instructor_id)
            ->getResult();
    */


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Question $entity, bool $flush = false): void
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
    public function remove(Question $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

//    /**
//     * @return Question[] Returns an array of Question objects
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

//    public function findOneBySomeField($value): ?Question
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getQuestionWithReleaseDate($question_id)
    {
             $questionBdd = $this->getEntityManager();
        return $questionBdd->createQuery('
        SELECT q.id, q.wording, qcmi.release_date
        FROM App\Entity\Question q
        INNER JOIN App\Entity\LinkQcmQuestion l
        WITH l.question = q.id
        INNER JOIN App\Entity\Qcm qcm
        WITH l.qcm = qcm.id
        INNER JOIN App\Entity\QcmInstance qcmi
        WITH qcmi.qcm = qcm.id
        WHERE q.id = 3
        ')
//            ->setParameter('id_question', $question_id)
            ->getResult();

    }


}