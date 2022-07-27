<?php

namespace App\Controller;

use App\Entity\Enum\Level;
use App\Entity\LinkSessionModule;
use App\Entity\LinkSessionStudent;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Result;
use App\Repository\LinkSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmRepository;
use App\Repository\ResultRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student/qcms', name: 'student_qcms', methods: ['GET'])]
    public function manageQcms( StudentRepository $studentRepo, LinkSessionStudentRepository $linkSessionStudentRepo, LinkSessionModuleRepository $linkSessionModuleRepo, ModuleRepository $moduleRepo): Response
    {
        $student = $studentRepo->find( 20 ); // changer l'id pour l'id de l'etudiant qui est log

        // Recupérer l'instance de QCM pour laquelle la date du jour se trouve entre release_date et end_date pour l'etudiant connecté
        $allAvailableQcmInstances = $student->getQcmInstances();
        $officialQcmOfTheWeek  = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return
                $qcmInstance->getQcm()->getIsOfficial() == true
                && $qcmInstance->getReleaseDate() < new \DateTime()
                && $qcmInstance->getEndDate() > new \DateTime()
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

    #[Route('student/qcms/Done', name: 'student_qcms_done', methods: ['GET'])]
    public function qcmsDone( StudentRepository $studentRepo, LinkSessionStudentRepository $linkSessionStudentRepo, LinkSessionModuleRepository $linkSessionModuleRepo )
    {
        $student = $studentRepo->find( 12 ); // changer l'id pour l'id de l'etudiant qui est log

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

    #[Route('student/qcm/qcmToDo/{qcmInstance}', name: 'student_qcm_to_do', methods: ['GET', 'POST'])]
    public function QcmToDo( QcmInstance $qcmInstance, QcmRepository $qcmRepository,StudentRepository $studentRepository, Request $request,  EntityManagerInterface $em){

        // Récupere le qcm par rapport à l'id du qcmInstance
        $qcm = $qcmRepository->find(['id' => ($qcmInstance->getQcm()->getId())]);

        // Retourne les questions avec leurs réponses sous forme de tableau
        $questionAnswersDecode = array_map(function($questionAnswer){
                $questionsDecode =(array)json_decode($questionAnswer)[0];
                $questionsDecode['question'] = (array)$questionsDecode['question'];
                $questionsDecode['question']['answers'] = (array)$questionsDecode['question']['answers'];
                foreach ($questionsDecode['question']['answers'] as $key => $value){
                    $questionsDecode['question']['answers'][$key] =  (array)$value;
                }
            return $questionsDecode['question'];
        },$qcm->getQuestionsAnswers());

        // Récupere les datas du form
        $result = $request->query->all();

        $countIsCorrectAnswer = 0;

//      Si pas vide
        if (count($result) !== 0) {

            // Traitement des réponses et ajout d'information dans le tableau pour json ensuite et add db
            foreach ($questionAnswersDecode as $questionDbKey => $questionDbValue) {
                foreach ($result as $studentAnswerKey => $studentAnswerValue) {
                    if ($questionAnswersDecode[$questionDbKey]['id'] == $studentAnswerKey) {
                        // Radio
                        if ($questionAnswersDecode[$questionDbKey]['responce_type'] === "radio") {
                            $studentAnswerValue = intval($studentAnswerValue);
                            foreach ($questionAnswersDecode[$questionDbKey]['answers'] as $answerKey => $answerValue) {
                                //Si case cochée par l'etudiant et bonne réponse
                                if (
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['is_correct'] === true
                                    &&
                                    $studentAnswerValue === $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['id']
                                ) {
                                    $countIsCorrectAnswer++;
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer'] = 1;
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer_wording'] = $studentAnswerValue;
                                }
                                // Si case cochée par l'etudiant
                                elseif ($studentAnswerValue === $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['id']) {
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer'] = 1;
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer_wording'] = $studentAnswerValue;
                                }
                                // Si pas case cochée par l'etudiant
                                else {
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer'] = 0;
                                    $questionAnswersDecode[$questionDbKey]['answers'][$answerKey]['student_answer_wording'] = $studentAnswerValue;
                                }
                            }
                        } // CheckBox
                        else {
                            $dbAnswersCheck = [
                                'good' => [],
                                'bad' => []
                            ];
                            foreach ($questionAnswersDecode[$questionDbKey]['answers'] as $answerDbKey => $answerDbValue)
                            {
                                if( $questionAnswersDecode[$questionDbKey]['answers'][$answerDbKey]['is_correct'] )
                                {
                                    $dbAnswersCheck['good'][] = $questionAnswersDecode[$questionDbKey]['answers'][$answerDbKey]['id'];
                                }
                                else
                                {
                                    $dbAnswersCheck['bad'][] = $questionAnswersDecode[$questionDbKey]['answers'][$answerDbKey]['id'];
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

                            foreach ($questionAnswersDecode[$questionDbKey]['answers'] as $answerDbKey => $answerDbValue)
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
            $student = $studentRepository->find(2);
            $result = new Result();
            $result->setStudent($student);
            $result->setQcmInstance($qcmInstance);
            $result->setTotalScore($totalScore);
            if( $totalScore < 25 )
            {
                $result->setLevel(Level::Discover);
            }
            elseif( $totalScore >= 25 && $totalScore < 50 )
            {
                $result->setLevel(Level::Explore);
            }
            elseif( $totalScore >= 50 && $totalScore < 75 )
            {
                $result->setLevel(Level::Master);
            }
            elseif( $totalScore >= 75 && $totalScore <= 100 )
            {
                $result->setLevel(Level::Dominate);
            }

            foreach ($questionAnswersDecode as $questionAnswersKey => $questionAnswersValue){
                $questionAnswersDecode[$questionAnswersKey] = json_encode($questionAnswersDecode[$questionAnswersKey]);
            }
            $result->setAnswers($questionAnswersDecode);
            $result->setInstructorComment(null);

            //  validation et enregistrement des données du form dans la bdd
            $em->persist($result);
            $em->flush();

            $this->addFlash('success', 'Le qcm a bien été enregistré.');
            return $this->redirectToRoute('student_qcmsdone');
        }

        return $this->render('student/qcm_to_do.html.twig', [
            'idQcmInstance' => $qcmInstance->getId(),
            'nameQcmInstance' => $qcmInstance->getName(),
            'titleModule'=> $qcm->getModule()->getTitle(),
            'questionsAnswers' => $questionAnswersDecode
        ]);
    }

    #[Route('student/qcm/qcmDone/{qcmInstance}', name: 'student_qcm_done', methods: ['GET'])]
    public function QcmDone( $qcmInstance, QcmRepository $qcmRepository,ResultRepository $resultRepository,StudentRepository $studentRepository, Request $request,  EntityManagerInterface $em)
    {
        $studentId = 2;
        $resutl = $resultRepository->findBy(['qcmInstance'=>$qcmInstance, 'student'=>$studentId] );
        dump(gettype($resutl[0]));

        dump($resutl[0]->getAnswers());
        dump(json_decode($resutl[0]->getAnswers()[0]));
        $questionsAnswersDecode = [];
        foreach ($resutl[0]->getAnswers() as $result){
            $questionsAnswersDecode[] = json_decode($result);
        }

        return $this->render('student/qcmDone.twig', [
            'questionsAnswers' => $questionsAnswersDecode
        ]);
    }


    }