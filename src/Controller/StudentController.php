<?php

namespace App\Controller;

use App\Entity\Enum\Level;
use App\Entity\Main\LinkSessionModule;
use App\Entity\Main\Module;
use App\Entity\Main\Qcm;
use App\Entity\Main\QcmInstance;
use App\Entity\Main\Result;
use App\Helpers\QcmGeneratorHelper;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProposalRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResultRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StudentController extends AbstractController
{
//    /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
    public function __construct(StudentRepository $studentRepository){
        $this->studentRepo = $studentRepository;
        $this->id = 11;
    }

    #[Route('/student/qcms', name: 'student_qcms', methods: ['GET'])]
    public function manageQcms(
        LinkSessionStudentRepository $linkSessionStudentRepo,
        LinkInstructorSessionModuleRepository $linkSessionModuleRepo,
        ModuleRepository $moduleRepo,
    ): Response
    {

        $student = $this->studentRepo->find($this->id);

        $allAvailableQcmInstances = $student->getQcmInstances();

        $officialQcmOfTheWeek = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return
                $qcmInstance->getQcm()->getIsOfficial() == true
                && $qcmInstance->getStartTime() < new \DateTime()
                && $qcmInstance->getEndTime() > new \DateTime()
                && $qcmInstance->getQcm()->getIsEnabled() == true ;
        });

        $unofficialQcmInstances = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return $qcmInstance->getQcm()->getIsOfficial() == false;
        });

        $studentQcmInstances = $student->getQcmInstances();
        $qcmResults = [];
        foreach ($studentQcmInstances as $studentQcmInstance){
            if ($studentQcmInstance->getResult() !== null){
                $qcmResults[]= $studentQcmInstance->getResult();
            }
        }
        $unofficialQcmInstancesDone = [];
        foreach ($qcmResults as $qcmResult)
        {
            if( !$qcmResult->getQcmInstance()->getQcm()->getIsOfficial() ) {
                $unofficialQcmInstancesDone[] = $qcmResult->getQcmInstance();
            }
        }
        $unofficialQcmNotDone = [];
        foreach( $unofficialQcmInstances as $unofficialQcmInstance )
        {
            if( !in_array($unofficialQcmInstance, $unofficialQcmInstancesDone) )
            {
                $unofficialQcmNotDone[] = $unofficialQcmInstance;
            }
        }

        $studentSession = $linkSessionStudentRepo->findOneBy([ 'student' => $student->getId(), 'isEnabled'=> 1] )->getSession();
        $sessionModules = $linkSessionModuleRepo->findBy([ 'session' => $studentSession->getId() ]);
        foreach ( $sessionModules as $key => $sessionModule )
        {
            $sessionModules[$key] = $sessionModule->getModule();
        }

        $endedLinkSessionModules = $studentSession->getLinksSessionModule()->filter(function(LinkSessionModule $linkSessionModule){
            return $linkSessionModule->getEndDate() < new \DateTime();
        });

        $endedModules = [];
        foreach($endedLinkSessionModules as $endedLinkSessionModule)
        {
            $endedModules[] = $endedLinkSessionModule->getModule();
        }

        $accomplishedModules = $moduleRepo->getAccomplishedModules( $student->getId() );

        $accomplishedModulesIds = [];
        foreach($accomplishedModules as $accomplishedModule)
        {
            $accomplishedModulesIds[] = $accomplishedModule['id'];
        }

        $retryableModules = [];
        foreach( $endedModules as $endedModule )
        {
            if( !in_array( $endedModule->getId(), $accomplishedModulesIds ) )
            {
                $retryableModules[] = $endedModule;
            }
        }

        return $this->render('student/qcms.html.twig', [
            'student'                       => $student,
            'qcmOfTheWeek'                  => $officialQcmOfTheWeek,
            'unofficialQcmInstancesNotDone' => $unofficialQcmNotDone,
            'sessionModules'                => $sessionModules,
            'retryableModules'              => $retryableModules,
        ]);
    }

    #[Route('student/qcms/done', name: 'student_qcms_done', methods: ['GET'])]
    public function qcmsDone(
        LinkSessionStudentRepository $linkSessionStudentRepo,
        LinkInstructorSessionModuleRepository $linkSessionModuleRepo
    ): Response
    {
        /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
        $student = $this->studentRepo->find($this->id);
//        $student = $this->getUser();

        $studentQcmInstances = $student->getQcmInstances();
        $studentResults = [];
        foreach ($studentQcmInstances as $studentQcmInstance){
            if ($studentQcmInstance->getResult() !== null){
                $studentResults[]= $studentQcmInstance->getResult();
            }
        }
        $qcmsDone = [];
        foreach($studentResults as $studentResult)
        {
            $qcmInstance = $studentResult->getQcmInstance();
            $qcmsDone[] = [
                'qcm'    => $qcmInstance->getQcm(),
                'result' => $studentResult,
                'module' => $qcmInstance->getQcm()->getModule()->getTitle(),
            ];
        }

        $studentSession = $linkSessionStudentRepo->findOneBy([ 'student' => $student] )->getSession();
        $sessionModules = $linkSessionModuleRepo->findBy([ 'session' => $studentSession ]);
        foreach ( $sessionModules as $key => $sessionModule )
        {
            $sessionModules[$key] = $sessionModule->getModule();
        }

        return $this->render('student/qcms_done.html.twig', [
            'qcmsDone' => $qcmsDone,
            'modules'  => $sessionModules
        ]);
    }

    #[Route('student/qcms/qcmToDo/{qcmInstance}', name: 'student_qcm_to_do', methods: ['GET', 'POST'])]
    public function QcmToDo(
        QcmInstance $qcmInstance,
        QcmRepository $qcmRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
        $student = $this->studentRepo->find($this->id);
//        $student = $this->getUser();
        $qcm = $qcmRepository->find(['id' => ($qcmInstance->getQcm()->getId())]);

        $questionsCache = $qcm->getQuestionsCache();

        $resultRequest = $request->query->all();
        $countIsCorrectAnswer = 0;

        if( count($resultRequest) !== 0 )
        {
            foreach ( $questionsCache as $questionCacheKey => $questionCache )
            {
                foreach ( $resultRequest as $studentAnswerKey => $studentAnswerValue )
                {
                    if( $questionsCache[$questionCacheKey]['id'] == $studentAnswerKey )
                    {
                        // Radio
                        if ( !$questionsCache[$questionCacheKey]['isMultiple'] )
                        {
                            $studentAnswerValue = intval($studentAnswerValue);
                            foreach ($questionsCache[$questionCacheKey]['proposals'] as $proposalKey => $proposal)
                            {
                                //Si case cochée par l'etudiant et bonne réponse
                                if(
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isCorrectAnswer']
                                    &&
                                    $studentAnswerValue === $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id']
                                )
                                {
                                    $countIsCorrectAnswer++;
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 1;
                                    $questionsCache[$questionCacheKey]['student_answer_correct'] = 1;
                                }
                                // Si case cochée par l'etudiant
                                elseif( $studentAnswerValue === $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id'] )
                                {
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 1;
                                    $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
                                }
                                // Si pas case cochée par l'etudiant
                                else
                                {
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 0;
                                    $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
                                }
                            }
                        } // CheckBox
                        else
                        {
                            $dbAnswersCheck = [
                                'good' => [],
                                'bad' => []
                            ];
                            foreach( $questionsCache[$questionCacheKey]['proposals'] as $proposalKey => $proposal )
                            {
                                if( $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isCorrectAnswer'] )
                                {
                                    $dbAnswersCheck['good'][] = $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id'];
                                }
                                else
                                {
                                    $dbAnswersCheck['bad'][] = $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id'];
                                }
                            }
                            $goodAnswersCount = 0;
                            $badAnswersCount = 0;
                            foreach ($studentAnswerValue as $studentAnswer)
                            {
                                if( in_array( $studentAnswer, $dbAnswersCheck['good'] ) )
                                {
                                    $goodAnswersCount++;
                                    $questionsCache[$questionCacheKey]['student_answer_correct'] = 1;
                                }
                                elseif( in_array($studentAnswer, $dbAnswersCheck['bad']) )
                                {
                                    $badAnswersCount++;
                                    $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
                                }
                            }

                            foreach ($questionsCache[$questionCacheKey]['proposals'] as $answerDbKey => $answerDbValue)
                            {
                                if( in_array($answerDbValue['id'], $studentAnswerValue) )
                                {
                                    $questionsCache[$questionCacheKey]['proposals'][$answerDbKey]['isStudentAnswer'] = 1;
                                }
                                else
                                {
                                    $questionsCache[$questionCacheKey]['proposals'][$answerDbKey]['isStudentAnswer'] = 0;
                                }
                            }


                            /*TODO A tester Dorine (si isCorrect est bien ajouté au tableau / Problème de co avec Google Auth peux pas tester) */
                            if( $goodAnswersCount === count($dbAnswersCheck['good']) && $badAnswersCount === 0 )
                            {
                                $countIsCorrectAnswer ++;
                                $questionsCache[$questionCacheKey]['isCorrect'] = true;
                            }else{
                                $questionsCache[$questionCacheKey]['isCorrect'] = false;
                            }
                        }
                    }
                }
            }

            $nbQuestions = count($questionsCache);
            $totalScore = (100/$nbQuestions)*$countIsCorrectAnswer;

            $result = new Result();

            $result->setQcmInstance($qcmInstance);
            $result->setScore($totalScore);
            if( $totalScore < 25 )
            {
                $result->setLevel(Level::Discover->value);
            }
            elseif( $totalScore >= 25 && $totalScore < 50 )
            {
                $result->setLevel(Level::Explore->value);
            }
            elseif( $totalScore >= 50 && $totalScore < 75 )
            {
                $result->setLevel(Level::Master->value);
            }
            elseif( $totalScore >= 75 && $totalScore <= 100 )
            {
                $result->setLevel(Level::Dominate->value);
            }

            $qcmInstances = $qcm->getQcmInstances()->filter( function( $qcmInstance ) use ($student) {
                return $qcmInstance->getStudent() === $student;
            });
            if( $qcmInstances && $qcm->getIsOfficial() )
            {
                $isFirstTry = false;
            }
            else
            {
                $isFirstTry = true;
            }

            $result->setIsFirstTry($isFirstTry);

            $result->setAnswers($questionsCache);
//            dd($resultRequest);
            if (trim($resultRequest['comment_student'] === "")){
                $result->setStudentComment(null);
            }else{
                $result->setStudentComment(trim($resultRequest['comment_student']));
            }
            $result->setInstructorComment(null);

            $em->persist($result);
            $em->flush();

            $this->addFlash('success', 'Le qcm a bien été enregistré.');
            return $this->redirectToRoute('student_qcms_done');
        }

        return $this->render('student/qcm_to_do.html.twig', [
            'idQcmInstance' => $qcmInstance->getId(),
            'nameQcmInstance' => $qcmInstance->getQcm()->getTitle(),
            'titleModule'=> $qcm->getModule()->getTitle(),
            'questionsAnswers' => $questionsCache
        ]);
    }

    #[Route('student/qcm/training', name: 'student_qcm_training', methods: ['GET']) ]
    public function qcmTraining(
        Request $request,
        ModuleRepository $moduleRepo,
        QuestionRepository $questionRepo,
        Security $security,
        EntityManagerInterface $manager
    ): Response
    {
        $module = $moduleRepo->find( $request->get('module') );
        $difficulty = (int) $request->get('difficulty');

        $student = $this->getUser();

        $qcmGenerator = new QcmGeneratorHelper( $questionRepo, $security);
        $trainingQcm = $qcmGenerator->generateRandomQcm( $module, true, $difficulty, $this->studentRepo );

        $manager->persist( $trainingQcm );
        $manager->flush();

        $trainingQcmInstance = new QcmInstance();
        $trainingQcmInstance->setStudent( $student );
        $trainingQcmInstance->setQcm( $trainingQcm );
        $trainingQcmInstance->setStartTime( new \DateTime() );
        $endTime = new \DateTime();
        $trainingQcmInstance->setEndTime( $endTime->add( new \DateInterval('P1D') ) );
        $trainingQcmInstance->setCreatedAtValue();
        $trainingQcmInstance->setUpdateAtValue();

        $manager->persist( $trainingQcmInstance );
        $manager->flush();

        return $this->redirectToRoute('student_qcm_to_do', [
            'qcmInstance' => $trainingQcmInstance->getId()
        ]);
    }

    #[Route('student/qcm/retry_for_badges/{module}', name: 'student_retry_for_badges', methods: ['GET'])]
    public function retryQcmToGetBadge(
        QuestionRepository $questionRepo,
        Module $module,
        Security $security,
        EntityManagerInterface $manager
    ): Response
    {
        $student = $this->getUser();

        $qcmGenerator = new QcmGeneratorHelper( $questionRepo, $security);
        $retryQcm = $qcmGenerator->generateRandomQcm( $module );

        $manager->persist( $retryQcm );
        $manager->flush();

        $qcmInstanceRetry = new QcmInstance();
        $qcmInstanceRetry->setStudent( $student );
        $qcmInstanceRetry->setQcm( $retryQcm );
        $qcmInstanceRetry->setStartTime( new \DateTime() );
        $endTime = new \DateTime();
        $qcmInstanceRetry->setEndTime( $endTime->add( new \DateInterval('P1D') ) );
        $qcmInstanceRetry->setCreatedAtValue();
        $qcmInstanceRetry->setUpdateAtValue();

        $manager->persist( $qcmInstanceRetry );
        $manager->flush();

        $manager->persist( $qcmInstanceRetry );
        $manager->flush();

        return $this->redirectToRoute('student_qcm_to_do', [
            'qcmInstance'    => $qcmInstanceRetry->getId(),
        ]);
    }

    #[Route('student/qcm/retry_same_qcm/{qcm}', name: 'student_retry_same_qcm', methods: ['GET'])]
    public function retrySameQcm(
        Qcm $qcm,
        EntityManagerInterface $manager
    ): Response
    {
        $qcmInstance = new QcmInstance();
        $qcmInstance->setStudent( $this->getUser() );
        $qcmInstance->setQcm( $qcm );
        $qcmInstance->setStartTime( new \DateTime() );
        $endTime = new \DateTime();
        $qcmInstance->setEndTime( $endTime->add( new \DateInterval('P1D') ) );
        $qcmInstance->setCreatedAtValue();
        $qcmInstance->setUpdateAtValue();

        $manager->persist( $qcmInstance );
        $manager->flush();

        return $this->redirectToRoute('student_qcm_to_do', [
            'qcmInstance'    => $qcmInstance->getId(),
        ]);
    }

    #[Route('student/qcm/correction/{result}', name: 'student_qcm_correction', methods: ['GET'])]
    public function qcmCorrection(
        Result $result,
        QuestionRepository $questionRepo,
        ProposalRepository $proposalRepo
    ): Response
    {
        $dbAnswers = $result->getAnswers();
        $qcmInstance = $result->getQcmInstance();
        $qcm = $qcmInstance->getQcm();
        $qcmQuestions = [];

        foreach( $dbAnswers as $dbAnswer )
        {
            $question = $questionRepo->find( $dbAnswer['id'] );
            $proposals = [];
            foreach( $dbAnswer['proposals'] as $answer )
            {
                $proposal = $proposalRepo->find( $answer['id'] );
                $proposals[] = [
                    'id'              => $answer['id'],
                    'wording'         => $proposal->getWording(),
                    'isStudentAnswer' => $answer['isStudentAnswer'],
                    'isCorrectAnswer' => $answer['isCorrectAnswer'],
                ];
            }
//            dd($dbAnswer);
            $qcmQuestions[] = [
                'questionId'  => $dbAnswer['id'],
                'isMultiple'  => $question->getIsMultiple(),
                'wording'     => $question->getWording(),
                'answers'   => $proposals,
                'isCorrect' => $dbAnswer['student_answer_correct'],
                'explanation' => $question->getExplanation()
            ];
        }

        return $this->render('student/qcm_correction.html.twig', [
            'qcmQuestions' => $qcmQuestions,
            'nameQcmInstance' => $qcmInstance->getQcm()->getTitle(),
            'titleModule'=> $qcm->getModule()->getTitle(),
            'studentComment' => $result->getStudentComment(),
            'instructorComment' => $result->getInstructorComment(),
            'resultId' => $result->getId()
        ]);
    }
}