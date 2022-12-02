<?php

namespace App\Controller;

use App\Entity\Enum\Level;
use App\Entity\Main\LinkSessionModule;
use App\Entity\Main\LinkSessionStudent;
use App\Entity\Main\Module;
use App\Entity\Main\Qcm;
use App\Entity\Main\QcmInstance;
use App\Entity\Main\Result;
use App\Entity\Main\User;
use App\Helpers\QcmGeneratorHelper;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProposalRepository;
use App\Repository\QcmInstanceRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResultRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StudentController extends AbstractController
{

    private StudentRepository $studentRepo;
    private UserRepository $userRepo;
    private Security $security;

    public function __construct(StudentRepository $studentRepository, UserRepository $userRepository, Security $security){
        $this->studentRepo = $studentRepository;
        $this->userRepo = $userRepository;
        $this->security = $security;
    }

    #[Route('/student/qcms', name: 'student_qcms', methods: ['GET'])]
    public function manageQcms(
        LinkSessionStudentRepository $linkSessionStudentRepo,
        LinkInstructorSessionModuleRepository $linkSessionModuleRepo,
        ModuleRepository $moduleRepo,
    ): Response
    {

        $student = $this->studentRepo->find($this->security->getUser()->getId());

        $allAvailableQcmInstances = $student->getQcmInstances();

        if($allAvailableQcmInstances){
            /********************************************************************************************************/

            $officialQcmOfTheWeek = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
                return
                    $qcmInstance->getQcm()->getIsOfficial() == true
                    && $qcmInstance->getStartTime() < new \DateTime()
                    && $qcmInstance->getEndTime() > new \DateTime()
                    && $qcmInstance->getQcm()->getIsEnabled() == true
                    && $qcmInstance->getResult() === null;
            });
            /********************************************************************************************************/

            $unofficialQcmNotDone = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ) use ($student){
                return
                    $qcmInstance->getQcm()->getIsOfficial() === false
                    && $qcmInstance->getStartTime() < new \DateTime()
                    && $qcmInstance->getEndTime() > new \DateTime()
                    && $qcmInstance->getResult() === null
                    && $qcmInstance->getQcm()->getAuthor() !== $student
                    ;
            });
        }

        /********************************************************************************************************/

        $studentSession = $linkSessionStudentRepo->findOneBy([ 'student' => $student->getId(), 'isEnabled'=> 1] )->getSession();
        $sessionModules = $linkSessionModuleRepo->findBy([ 'session' => $studentSession->getId() ]);
        foreach ( $sessionModules as $key => $sessionModule )
        {
            $sessionModules[$key] = $sessionModule->getModule();
        }
        /********************************************************************************************************/

        $retryableModules = $moduleRepo->getRetryableModules( $student->getId() );

        $retryableModules = array_map( function ( $retryableModule ) use ($moduleRepo) {
            return $moduleRepo->find($retryableModule['moduleId']);
        }, $retryableModules );

        $retryableModules = array_unique($retryableModules);

        return $this->render('student/qcms.html.twig', [
            'student'                       => $student,
            'qcmOfTheWeek'                  => $officialQcmOfTheWeek ? $officialQcmOfTheWeek : [],
            'unofficialQcmInstancesNotDone' => $unofficialQcmNotDone ? $unofficialQcmNotDone : [],
            'sessionModules'                => $sessionModules,
            'retryableModules'              => $retryableModules,
        ]);
    }

    #[Route('student/qcms/done', name: 'student_qcms_done', methods: ['GET'])]
    public function qcmsDone(
        LinkSessionStudentRepository $linkSessionStudentRepo,
        LinkInstructorSessionModuleRepository $linkSessionModuleRepo,
        QcmInstanceRepository $qcmInstanceRepository
    ): Response
    {
        $student = $this->studentRepo->find($this->security->getUser()->getId());

        $studentQcmInstances = $qcmInstanceRepository->getQcmInstancesByStudent($this->security->getUser()->getId());
        /*TODO Code fonctionnel normalement, a été remplacer par la ligne de haut dessus temporairement */
//        $studentQcmInstances = $student->getQcmInstances();

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

            if(
                $qcmInstance->getQcm()->getIsOfficial() === true
                && $qcmInstance->getQcm()->getIsPublic() === true
                && $qcmInstance->getResult()->isFirstTry() === 1
                && $qcmInstance->getQcm()->getAuthor()->getId() !== $student->getId()
            )
            {
                $type = 'official';
            }
            elseif
            (
                $qcmInstance->getQcm()->getIsOfficial() === false
                && $qcmInstance->getResult()->isFirstTry() === 1
                && $qcmInstance->getQcm()->getAuthor()->getId() !== $student->getId()
            )
            {
                $type = 'exercice';
            }
            elseif
            (
                $qcmInstance->getQcm()->getIsOfficial() === true
                && $qcmInstance->getQcm()->getIsPublic() === false
                && $qcmInstance->getQcm()->getAuthor()->getId() === $student->getId()
            )
            {
                $type = 'retryBadge';
            }
            elseif
            (
                $qcmInstance->getResult()->isFirstTry() === 0
            )
            {
                $type = 'retry';
            }
            elseif
            (
                $qcmInstance->getQcm()->getIsOfficial() === false
                && $qcmInstance->getQcm()->getAuthor()->getId() === $student->getId()
            )
            {
                $type = 'trainning';
            }

            $qcmsDone[] = [
                'qcm'    => $qcmInstance->getQcm(),
                'result' => $studentResult,
                'module' => $qcmInstance->getQcm()->getModule()->getTitle(),
                'type' => $type,
                'isFirstTry' => $studentResult->isFirstTry()
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

    #[Route('student/qcms/qcmToDo/{qcmInstance}/{isForBadge}', name: 'student_qcm_to_do', methods: ['GET', 'POST'])]
    public function QcmToDo(
        QcmInstance $qcmInstance,
        QcmRepository $qcmRepository,
        ModuleRepository $moduleRepository,
        ResultRepository $resultRepository,
        Request $request,
        EntityManagerInterface $em,
        $isForBadge = null
    ): Response
    {

        $student = $this->studentRepo->find($this->security->getUser()->getId());

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
                            $radioIsCorrect = 0;
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
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 1;
                                    $radioIsCorrect ++ ;
                                }
                                // Si case cochée par l'etudiant
                                elseif(
                                    !$questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isCorrectAnswer']
                                    &&
                                    $studentAnswerValue === $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id']
                                )
                                {
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 1;
                                }
                                // Si pas case cochée par l'etudiant
                                else
                                {
                                    $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 0;
                                }
                            }
                            // Si l'étudiant a répondu juste
                            if ($radioIsCorrect !== 0)
                            {
                                $countIsCorrectAnswer++;
                                $questionsCache[$questionCacheKey]['student_answer_correct'] = 1;
                            }
                            else
                            {
                                $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
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

                                }
                                elseif( in_array($studentAnswer, $dbAnswersCheck['bad']) )
                                {
                                    $badAnswersCount++;

                                }
                                else{
                                    $badAnswersCount++;

                                }
                            }

                            // Pour savoir quelles réponses à coché l'étudiant
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

                            if( $goodAnswersCount === count($dbAnswersCheck['good']) && $badAnswersCount === 0 )
                            {
                                $countIsCorrectAnswer ++;
                                $questionsCache[$questionCacheKey]['student_answer_correct'] = 1;
                            }else{
                                $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
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
            if( (count($qcmInstances) > 1 ) )
            {
                $isFirstTry = false;
            }
            else
            {
                $isFirstTry = true;
            }

            $result->setIsFirstTry($isFirstTry);

            $result->setAnswers($questionsCache);

            if (trim($resultRequest['comment_student'] === "")){
                $result->setStudentComment(null);
            }else{
                $result->setStudentComment(trim($resultRequest['comment_student']));
            }
            $result->setInstructorComment(null);

            $em->persist($result);
            $em->flush();

            $this->addFlash('success', 'Le qcm a bien été enregistré.');

            if (
                ($qcmInstance->getQcm()->getIsOfficial() && !$isForBadge)
                ||
                ($qcmInstance->getQcm()->getIsOfficial() && $isForBadge)
            )
            {
                $titleModule = $qcmInstance->getQcm()->getModule()->getTitle();
                $moduleBaseName = preg_replace('/[0-9]+/', '' ,$titleModule );

                $modules = $moduleRepository->getModulesByModuleBaseName($student->getId(), $moduleBaseName);
                $qcmsSuccess = $resultRepository->getOfficialQcmsSuccessByModule($student->getId(), $moduleBaseName);

                if (count($modules) === count($qcmsSuccess))
                {
                    $studentBadges = $student->getBadges();
                    if (!$studentBadges)
                    {
                        $studentBadges = [];
                    }

                    $studentBadges[] = strtoupper($moduleBaseName) . '.svg';
                    $student->setBadges($studentBadges);
                    $em->persist($student);
                    $em->flush();
                }
            }

            return $this->redirectToRoute('student_qcms_done');
        }

        return $this->render('student/qcm_to_do.html.twig', [
            'idQcmInstance' => $qcmInstance->getId(),
            'nameQcmInstance' => $qcmInstance->getQcm()->getTitle(),
            'titleModule'=> $qcm->getModule()->getTitle(),
            'questionsAnswers' => $questionsCache,
            'isForBadge' => $isForBadge
        ]);
    }

    #[Route('student/qcm/training', name: 'student_qcm_training', methods: ['GET']) ]
    public function qcmTraining(
        Request $request,
        ModuleRepository $moduleRepo,
        QuestionRepository $questionRepo,
        Security $security,
        EntityManagerInterface $manager,
        UserRepository $userRepository
    ): Response
    {
        $module = $moduleRepo->find( $request->get('module') );

        $difficulty = (int) $request->get('difficulty');

        $student = $this->studentRepo->find($this->security->getUser()->getId());

        $qcmGenerator = new QcmGeneratorHelper( $questionRepo, $security);
        $trainingQcm = $qcmGenerator->generateRandomQcm( $module, $student, $userRepository ,$difficulty, 'training');

        $manager->persist( $trainingQcm );
        $manager->flush();

        $trainingQcmInstance = new QcmInstance();
        $trainingQcmInstance->setStudent( $student );
        $trainingQcmInstance->setDistributedBy( $student );
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
        EntityManagerInterface $manager,
        UserRepository $userRepository
    ): Response
    {

        $student = $this->studentRepo->find($this->security->getUser()->getId());

        $qcmGenerator = new QcmGeneratorHelper( $questionRepo, $security);
        $retryQcm = $qcmGenerator->generateRandomQcm( $module, $student, $userRepository, 2,'retryBadge' );
        $manager->persist( $retryQcm );
        $manager->flush();

        $qcmInstanceRetry = new QcmInstance();
        $qcmInstanceRetry->setStudent( $student );
        $qcmInstanceRetry->setDistributedBy( $student );
        $qcmInstanceRetry->setQcm( $retryQcm );
        $qcmInstanceRetry->setStartTime( new \DateTime() );
        $endTime = new \DateTime();
        $qcmInstanceRetry->setEndTime( $endTime->add( new \DateInterval('P1D') ) );
        $qcmInstanceRetry->setCreatedAtValue();
        $qcmInstanceRetry->setUpdateAtValue();

        $manager->persist( $qcmInstanceRetry );
        $manager->flush();

        return $this->redirectToRoute('student_qcm_to_do', [
            'qcmInstance'    => $qcmInstanceRetry->getId(),
            'isForBadge'     => 1
        ]);
    }

    #[Route('student/qcm/retry_same_qcm/{qcm}', name: 'student_retry_same_qcm', methods: ['GET'])]
    public function retrySameQcm(
        Qcm $qcm,
        EntityManagerInterface $manager
    ): Response
    {
        $qcmInstance = new QcmInstance();
        $student = $this->studentRepo->find( $this->security->getUser()->getId() );
        $qcmInstance->setStudent( $student );
        $qcmInstance->setDistributedBy($student);
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
        ProposalRepository $proposalRepo,
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

    #[Route('/student/level/', name: 'student_level', methods: ['GET'])]
    public function levelStudentByModule(ModuleRepository $moduleRepository, ResultRepository $resultRepository, LinkSessionStudentRepository $linkSessionStudentRepository): Response
    {

        $linkSessionStudent = $linkSessionStudentRepository->findBy(['student'=>$this->security->getUser()->getId(), 'isEnabled'=>1]);

        $result = $resultRepository->resultWithQcmOfficialByModule( $this->security->getUser()->getId(), $linkSessionStudent[0]->getSession()->getId() );
        // Créer un tableau de tableau avec comme key le nom de base des modules
        $moduleGroups = [];

        foreach ($result as $res)
        {
            $moduleGroupName = preg_replace('/[0-9]+/', '' ,$res['title'] );
            $isExistKey = array_key_exists($moduleGroupName, $moduleGroups );
            if ($isExistKey)
            {
                $moduleGroups[$moduleGroupName][] = $res;
            }
            else
            {
                $moduleGroups[$moduleGroupName] = [];
                $moduleGroups[$moduleGroupName][] = $res;
            }
        }

        // Trier par numéro de module
        foreach ( $moduleGroups  as $key => $moduleGroup )
        {
            usort($moduleGroups[$key], function ($a, $b) {
                $nrbModuleA = preg_replace('/[A-Z]+/', '' ,$a['title'] );
                $nrbModuleB = preg_replace('/[A-Z]+/', '' ,$b['title'] );

                if ($nrbModuleA < $nrbModuleB)
                {
                    return -1;
                }
                else
                {
                    return 1;
                }
            } );

            $totalPonderation = 0;
            $totalNotePonderated = 0;
            $ponderation = 1;
            foreach ($moduleGroup as $index => $res)
            {
                if ($index > 1)
                {
                    $ponderation *= 2;
                }

                $totalNotePonderated += ($res['score'] * $ponderation);
                $totalPonderation += $ponderation;
            }

            $totalScore = $totalNotePonderated / $totalPonderation;
            $moduleGroups[$key]['totalScore'] = $totalScore;

            if( $totalScore < 25 )
            {
                $moduleGroups[$key]['level'] = 1;
            }
            elseif( $totalScore >= 25 && $totalScore < 50 )
            {
                $moduleGroups[$key]['level'] = 2;
            }
            elseif( $totalScore >= 50 && $totalScore < 75 )
            {
                $moduleGroups[$key]['level'] = 3;
            }
            elseif( $totalScore >= 75 && $totalScore <= 100 )
            {
                $moduleGroups[$key]['level'] = 4;
            }
        }

        return $this->render('student/level_modules.html.twig', [
            'moduleGroups' => $moduleGroups !== [] ? $moduleGroups : false
        ]);
    }

    #[Route('student/progression/', name: 'student_progression', methods: ['GET'])]
    public function progressionStudent( LinkSessionStudentRepository $linkSessionStudentRepository, ResultRepository $resultRepository ): Response
    {
        $linkSessionStudent = $linkSessionStudentRepository->findBy(['student'=>$this->security->getUser()->getId(), 'isEnabled'=>1]);

        $isOfficialQcms = $resultRepository->isOfficialQcmLevel( $this->security->getUser()->getId(), $linkSessionStudent[0]->getSession()->getId() );

        $moduleGroups = [];

        foreach ($isOfficialQcms as $res)
        {
            $moduleGroupName = preg_replace('/[0-9]+/', '' ,$res['moduleTitle'] );
            $isExistKey = array_key_exists($moduleGroupName, $moduleGroups );
            if ($isExistKey)
            {
                $moduleGroups[$moduleGroupName][] = $res;
            }
            else
            {
                $moduleGroups[$moduleGroupName] = [];
                $moduleGroups[$moduleGroupName][] = $res;
            }
        }

        // Trier par numero de module (Ex : JS1)
        foreach ( $moduleGroups as $key => $moduleGroup )
        {
            usort($moduleGroups[$key], function ($a, $b) {
                $moduleNumberA = preg_replace('/[A-Z]+/', '' ,$a['moduleTitle'] );
                $moduleNumberB = preg_replace('/[A-Z]+/', '' ,$b['moduleTitle'] );

                if (intval($moduleNumberA) < intval($moduleNumberB))
                {
                    return -1;
                }
                else
                {
                    return 1;
                }
            });

            $countQcmsSuccess = 0;
            $countQcms = 0;
            foreach ( $moduleGroups[$key] as $res)
            {
                if ($res['level'] === 3 || $res['level'] === 4)
                {
                    $countQcmsSuccess ++;
                }
                $countQcms ++;
            }

            if ($countQcmsSuccess === $countQcms)
            {
                $moduleGroups[$key]['getBadge'] = true;
            }
            else
            {
                $moduleGroups[$key]['getBadge'] = false;
            }
        }

        return $this->render('student/progression.html.twig', [
            'moduleGroups' => $moduleGroups
        ]);
    }

    #[Route('student/dashboard', name: 'student_dashboard', methods: ['GET'])]
    public function studentDashboard(

    ): Response
    {
        return $this->render('student/welcome_student.html.twig', [

        ]);
    }


}
