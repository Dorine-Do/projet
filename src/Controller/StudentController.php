<?php

namespace App\Controller;

use App\Entity\LinkSessionModule;
use App\Entity\LinkSessionStudent;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Result;
use App\Repository\LinkSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'student')]
    public function index( StudentRepository $studentRepo, LinkSessionStudentRepository $linkSessionStudentRepo, LinkSessionModuleRepository $linkSessionModuleRepo, ModuleRepository $moduleRepo): Response
    {
        $student = $studentRepo->find( 13581 ); // changer l'id pour l'id de l'etudiant qui est log

        // Recupérer l'instance de QCM pour laquelle la date du jour se trouve entre release_date et end_date pour l'etudiant connecté
        $allAvailableQcmInstances = $student->getQcmInstances();
        $officialQcmOfTheWeek  = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return
                $qcmInstance->getQcm()->getIsOfficial() == true
                && $qcmInstance->getReleaseDate() < new \DateTime()
                && $qcmInstance->getEndDate() > new \DateTime()
                && $qcmInstance->getQcm()->getEnabled() == true ;
        });

        // Recupérer les de QCM ayant is_official false/0
        $unofficialQcmInstances = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return $qcmInstance->getQcm()->getIsOfficial() == false;
        });

        // Recupérer tous les QCM de la table result pour l'id de l'etudiant
        $qcmResults = $student->getResults();
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
        $endedLinkSessionModules = $studentSession->getLinkSessionModule()->filter(function(LinkSessionModule $linkSessionModule){
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
        // Rendering
        return $this->render('student/index.html.twig', [
            'student'                       => $student,
            'qcmOfTheWeek'                  => $officialQcmOfTheWeek,
            'unofficialQcmInstancesNotDone' => $unofficialQcmNotDone,
            'sessionModules'                => $sessionModules,
            'retryableModules'              => $retryableModules,
        ]);
    }

    #[Route('student/qcmsDone', name: 'student_qcmsdone')]
    public function qcmDone( StudentRepository $studentRepo, LinkSessionStudentRepository $linkSessionStudentRepo, LinkSessionModuleRepository $linkSessionModuleRepo )
    {
        $student = $studentRepo->find( 13581 ); // changer l'id pour l'id de l'etudiant qui est log

        $studentResults = $student->getResults();
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

        return $this->render('student/qcm_done.html.twig', [
            'qcmsDone' => $qcmsDone,
            'modules'  => $sessionModules
        ]);
    }

    #[Route('student/qcmToDo/{qcmInstance}', name: 'student_qcmToDo')]
    public function QcmToDo( QcmInstance $qcmInstance, QcmRepository $qcmRepository){

        $qcm = $qcmRepository->find(['id' => ($qcmInstance->getQcm()->getId())]);
//        dump(json_decode($qcm->getQuestionsAnswers()[0]));
        $questionsAnswers = [];
        foreach ($qcm->getQuestionsAnswers() as $questionAnswer){
            array_push($questionsAnswers, json_decode( $questionAnswer)[0]) ;
        }
//        dump($questionsAnswers);
//        dump($qcm);
//        dump($qcm->getModule()->getTitle());
//        dd($qcmInstance);




        return $this->render('student/qcm_to_do.html.twig', [
            'nameQcmInstance' => $qcmInstance->getName(),
            'titleModule'=> $qcm->getModule()->getTitle(),
            'questionsAnswers' => $questionsAnswers
        ]);
    }

}