<?php

namespace App\Controller;

use App\Entity\QcmInstance;
use App\Repository\QcmRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index( StudentRepository $studentRepo, QcmRepository $QcmRepo): Response
    {
        $student = $studentRepo->find( 11111 ); // changer l'id pour l'id de l'etudiant qui est log

        // Recupérer l'instance de QCM pour laquelle la date du jour se trouve entre release_date et end_date pour l'etudiant connecté
        $allAvailableQcmInstances = $student->getQcmInstances();
        $officialQcmOfTheWeek  = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return $qcmInstance->getQcm()->getIsOfficial() == true && $qcmInstance->getReleaseDate() < new \DateTime() && $qcmInstance->getEndDate() > new \DateTime();
        });

        // Recupérer les de QCM ayant is_official false/0
        $unofficialQcmInstances = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return $qcmInstance->getQcm()->getIsOfficial() == false;
        });

        // Recupérer tous les QCM de la table result pour l'id de l'etudiant
        $qcmResults = $student->getResults();
        $qcmInstancesDone = [];
        foreach ($qcmResults as $qcmResult)
        {
            $qcmInstancesDone[] = $$qcmResult->getQcmInstance();
        }
        $unofficialQcmNotDone = $allAvailableQcmInstances->filter(function( QcmInstance $qcmInstance ){
            return $qcmInstance->getQcm()->getIsOfficial() == false;
        });

        // Recupérer tous les QCM de la table result pour l'id de l'etudiant qui ont un total score < 50
        $qcmResultsUnderFifty = $qcmResults->filter(function(){
            //...
        });

        // Recupérer tous les modules liés à la session de l'élève

        return $this->render('student/index.html.twig', [
            'student'                => $student,
            'unofficialQcmInstances' => $unofficialQcmInstances,
            'qcmOfTheWeek'           => $officialQcmOfTheWeek,
        ]);
    }

    #[Route('/qcmDone', 'app_qcmdone')]
    public function qcmDone()
    {
        return $this->render('student/index.html.twig', [
            'msg' => 'A faire'
        ]);
    }
}