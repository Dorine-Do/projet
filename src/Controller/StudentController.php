<?php

namespace App\Controller;

use App\Entity\Enum\Level;
use App\Entity\LinkSessionModule;
use App\Entity\QcmInstance;
use App\Entity\Result;
use App\Helpers\QcmGeneratorHelper;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmInstanceRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResultRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StudentController extends AbstractController
{
    #[Route('/student/qcms', name: 'student_qcms', methods: ['GET'])]
    public function manageQcms(
        LinkSessionStudentRepository $linkSessionStudentRepo,
        LinkInstructorSessionModuleRepository $linkSessionModuleRepo,
        ModuleRepository $moduleRepo,
        Security $security
    ): Response
    {
        $student = $security->getUser();

        $allAvailableQcmInstances = $student->getQcmInstances();

        $officialQcmOfTheWeek  = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
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

        $studentSession = $linkSessionStudentRepo->findOneBy([ 'student' => $student->getId()] )->getSession();
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
        LinkInstructorSessionModuleRepository $linkSessionModuleRepo,
        Security $security
    ): Response
    {
        $student = $security->getUser();

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
                'result' => $studentResult
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
        QcmInstanceRepository $qcmInstanceRepository,
        QcmRepository $qcmRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $qcm = $qcmRepository->find(['id' => ($qcmInstance->getQcm()->getId())]);

        $questionAnswersDecode = $qcm->getQuestionsCache();

        $result = $request->query->all();

        $countIsCorrectAnswer = 0;

        if (count($result) !== 0) {

            foreach ($questionAnswersDecode as $questionDbKey => $questionDbValue) {
                foreach ($result as $studentAnswerKey => $studentAnswerValue) {
                    if ($questionAnswersDecode[$questionDbKey]['id'] == $studentAnswerKey) {
                        // Radio
                        if ( !$questionAnswersDecode[$questionDbKey]['isMultiple'] ) {
                            $studentAnswerValue = intval($studentAnswerValue);
                            foreach ($questionAnswersDecode[$questionDbKey]['proposals'] as $answerKey => $answerValue) {
                                //Si case cochée par l'etudiant et bonne réponse
                                if (
                                    $questionAnswersDecode[$questionDbKey]['proposals'][$answerKey]['isCorrectAnswer']
                                    &&
                                    $studentAnswerValue === $questionAnswersDecode[$questionDbKey]['proposals'][$answerKey]['id']
                                ) {
                                    $countIsCorrectAnswer++;
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer'] = 1;
                                }
                                // Si case cochée par l'etudiant
                                elseif ($studentAnswerValue === $questionAnswersDecode[$questionDbKey]['proposals'][$answerKey]['id']) {
                                    $questionAnswersDecode['answers'][$answerKey]['student_answer'] = 1;
                                }
                                // Si pas case cochée par l'etudiant
                                else {
                                    $questionAnswersDecode['answers'][$answerKey]['student_answer'] = 0;
                                }
                            }
                        } // CheckBox
                        else
                        {
                            $dbAnswersCheck = [
                                'good' => [],
                                'bad' => []
                            ];
                            foreach ($questionAnswersDecode[$questionDbKey]['proposals'] as $answerDbKey => $answerDbValue)
                            {
                                if( $questionAnswersDecode[$questionDbKey]['proposals'][$answerDbKey]['isCorrectAnswer'] )
                                {
                                    $dbAnswersCheck['good'][] = $questionAnswersDecode[$questionDbKey]['proposals'][$answerDbKey]['id'];
                                }
                                else
                                {
                                    $dbAnswersCheck['bad'][] = $questionAnswersDecode[$questionDbKey]['proposals'][$answerDbKey]['id'];
                                }
                            }
                            $goodAnswersCount = 0;
                            $badAnswersCount = 0;
                            foreach ($studentAnswerValue as $studentAnswer)
                            {
                                if( in_array($studentAnswer, $dbAnswersCheck['good']) )
                                {
                                    $goodAnswersCount++;
                                    $questionAnswersDecode[$questionDbKey]['student_answer_correct'] = 1;
                                }
                                elseif( in_array($studentAnswer, $dbAnswersCheck['bad']) )
                                {
                                    $badAnswersCount++;
                                    $questionAnswersDecode[$questionDbKey]['student_answer_correct'] = 0;
                                }
                            }

                            foreach ($questionAnswersDecode[$questionDbKey]['proposals'] as $answerDbKey => $answerDbValue)
                            {
                                if( in_array($answerDbValue['id'], $studentAnswerValue) )
                                {
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerDbKey]['student_answer'] = 1;
                                }
                                else
                                {
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerDbKey]['student_answer'] = 0;
                                }
                            }

                            if( $goodAnswersCount === count($dbAnswersCheck['good']) && $badAnswersCount === 0 )
                            {
                                $countIsCorrectAnswer ++;
                            }
                        }
                    }
                }
            }

            $nbQuestions = count($questionAnswersDecode);
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

            foreach ($questionAnswersDecode as $questionAnswersKey => $questionAnswersValue){
                $questionAnswersDecode[$questionAnswersKey] = json_encode($questionAnswersDecode[$questionAnswersKey]);
            }

            $isAlreadyTryed = $qcmInstanceRepository->findBy( ['id' => $qcmInstance->getQcm()->getId()] );
            if( $isAlreadyTryed )
            {
                $isFirstTry = false;
            }
            else
            {
                $isFirstTry = false;
            }
            $result->setIsFirstTry($isFirstTry);

            $result->setAnswers($questionAnswersDecode);
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
            'questionsAnswers' => $questionAnswersDecode
        ]);
    }

    #[Route('student/qcm/qcmDone/{qcmInstance}', name: 'student_qcm_done', methods: ['GET'])]
    public function qcmDone(
        QcmInstance $qcmInstance,
        ResultRepository $resultRepository,
        Security $security
    ): Response
    {
        $result = $resultRepository->findOneBy( [ 'qcmInstance' => $qcmInstance, 'student'=> $security->getUser() ] );

        $questionsAnswersDecode = [];
        foreach ($result->getAnswers() as $answer){
            $questionsAnswersDecode[] = json_decode($answer);
        }

        return $this->render('student/qcmDone.twig', [
            'questionsAnswers' => $questionsAnswersDecode
        ]);
    }

    #[Route('student/qcm/training', name: 'student_qcm_training', methods: ['GET']) ]
    public function qcmTraining(
        Request $request,
        ModuleRepository $moduleRepo,
        StudentRepository $studentRepo,
        QuestionRepository $questionRepo,
        UserRepository $userRepo,
        Security $security,
        EntityManagerInterface $manager
    ): Response
    {
        $module = $moduleRepo->find( $request->get('module') );
        $difficulty = (int) $request->get('difficulty');
        $student = $studentRepo->findOneBy( ['email' => $security->getUser()->getUserIdentifier()] );

        $qcmGenerator = new QcmGeneratorHelper( $questionRepo, $userRepo, $security);
        $trainingQcm = $qcmGenerator->generateRandomQcm( $module, true, $difficulty );

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

        $manager->persist( $trainingQcm );
        $manager->flush();

        return $this->redirectToRoute('student_qcm_to_do', [
            'qcmInstance' => $trainingQcmInstance->getId()
        ]);
    }
}