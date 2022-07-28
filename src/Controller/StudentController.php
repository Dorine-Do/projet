<?php

namespace App\Controller;

use App\Entity\Enum\Level;
use App\Entity\LinkSessionModule;
use App\Entity\LinkSessionStudent;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Result;
use App\Helpers\QcmHelper;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmInstanceRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResultRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StudentController extends AbstractController
{
    #[Route('/student/qcms', name: 'student_qcms', methods: ['GET'])]
    public function manageQcms( StudentRepository $studentRepo, LinkSessionStudentRepository $linkSessionStudentRepo, LinkInstructorSessionModuleRepository $linkSessionModuleRepo, ModuleRepository $moduleRepo, Security $security): Response
    {
        $student = $studentRepo->findOneBy( ['email' => $security->getUser()->getUserIdentifier()] );

        // Recupérer l'instance de QCM pour laquelle la date du jour se trouve entre release_date et end_date pour l'etudiant connecté
        $allAvailableQcmInstances = $student->getQcmInstances();
        /* L'entity ne contient pas le nom du qcm donc dans le template, nous avons dû appeller qcm puis title */
        $officialQcmOfTheWeek  = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return
                $qcmInstance->getQcm()->getIsOfficial() == true
                && $qcmInstance->getStartTime() < new \DateTime()
                && $qcmInstance->getEndTime() > new \DateTime()
                && $qcmInstance->getQcm()->getIsEnabled() == true ;
        });

        // Recupérer les de QCM ayant is_official false/0
        $unofficialQcmInstances = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return $qcmInstance->getQcm()->getIsOfficial() == false;
        });
        // Recupérer tous les QCM de la table result pour l'id de l'etudiant
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

        // Recupérer tous les modules liés à la session de l'élève
        $studentSession = $linkSessionStudentRepo->findOneBy([ 'student' => $student->getId()] )->getSession();
        $sessionModules = $linkSessionModuleRepo->findBy([ 'session' => $studentSession->getId() ]);
        foreach ( $sessionModules as $key => $sessionModule )
        {
            $sessionModules[$key] = $sessionModule->getModule();
        }

        // Recupérer tous les QCM de la table result pour l'id de l'etudiant qui ont un total score < 50
        $endedLinkSessionModules = $studentSession->getLinksSessionModule()->filter(function(LinkSessionModule $linkSessionModule){
            return $linkSessionModule->getEndDate() < new \DateTime();
        });

        $endedModules = [];
        foreach($endedLinkSessionModules as $endedLinkSessionModule)
        {
            $endedModules[] = $endedLinkSessionModule->getModule();
        }

        $accomplishedModules = $moduleRepo->getAccomplishedModules( $student->getId() );
//        dd('stop');
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
        // Rendering
        return $this->render('student/qcms.html.twig', [
            'student'                       => $student,
            'qcmOfTheWeek'                  => $officialQcmOfTheWeek,
            'unofficialQcmInstancesNotDone' => $unofficialQcmNotDone,
            'sessionModules'                => $sessionModules,
            'retryableModules'              => $retryableModules,
        ]);
    }

    #[Route('student/qcms/done', name: 'student_qcms_done', methods: ['GET'])]
    public function qcmsDone( StudentRepository $studentRepo, LinkSessionStudentRepository $linkSessionStudentRepo, LinkInstructorSessionModuleRepository $linkSessionModuleRepo, Security $security )
    {
        $student = $studentRepo->findOneBy( ['email' => $security->getUser()->getUserIdentifier()] );

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

        $studentSession = $linkSessionStudentRepo->findOneBy([ 'student' => $student->getId()] )->getSession();
        $sessionModules = $linkSessionModuleRepo->findBy([ 'session' => $studentSession->getId() ]);
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
    public function QcmToDo( QcmInstance $qcmInstance, QcmInstanceRepository $qcmInstanceRepository, QcmRepository $qcmRepository,StudentRepository $studentRepository, Request $request,  EntityManagerInterface $em){

        // Récupere le qcm par rapport à l'id du qcmInstance
        $qcm = $qcmRepository->find(['id' => ($qcmInstance->getQcm()->getId())]);

        // Retourne les questions avec leurs réponses sous forme de tableau
        $questionAnswersDecode = $qcm->getQuestionsCache();

        // Récupere les datas du form
        $result = $request->query->all();

        $countIsCorrectAnswer = 0;

        // Si pas vide
        if (count($result) !== 0) {

            // Traitement des réponses et ajout d'information dans le tableau pour json ensuite et add db
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
                        else {
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

            //Resultats
            //En points
            $nbQuestions = count($questionAnswersDecode);
            $totalScore = (100/$nbQuestions)*$countIsCorrectAnswer;

            /*TODO A changer quand le système de connection sera opérationnel*/
            $student = $studentRepository->find(30);
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

            //  validation et enregistrement des données du form dans la bdd
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
    public function qcmDone( $qcmInstance, QcmRepository $qcmRepository,ResultRepository $resultRepository,StudentRepository $studentRepository, Request $request,  EntityManagerInterface $em, StudentRepository $studentRepo, Security $security)
    {
        $studentId = $studentRepo->findOneBy( ['email' => $security->getUser()->getUserIdentifier()] )->getId();
        $result = $resultRepository->findBy(['qcmInstance'=>$qcmInstance, 'student'=>$studentId] );

        $questionsAnswersDecode = [];
        foreach ($result[0]->getAnswers() as $answer){
            $questionsAnswersDecode[] = json_decode($answer);
        }

        return $this->render('student/qcmDone.twig', [
            'questionsAnswers' => $questionsAnswersDecode
        ]);
    }

    #[Route('student/qcm/training', name: 'student_qcm_training', methods: ['GET']) ]
    public function qcmTraining( Request $request, ModuleRepository $moduleRepo, StudentRepository $studentRepo, QuestionRepository $questionRepo, UserRepository $userRepo, Security $security, EntityManagerInterface $manager ): Response
    {
        $module = $moduleRepo->find( $request->get('module') );
        $difficulty = (int) $request->get('difficulty');
        $student = $studentRepo->findOneBy( ['email' => $security->getUser()->getUserIdentifier()] );

        $qcmGenerator = new QcmHelper( $questionRepo, $userRepo, $security);
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

        $this->redirectToRoute('student_qcm_to_do', [
            'qcmInstance' => $trainingQcmInstance->getId()
        ]);
    }
}