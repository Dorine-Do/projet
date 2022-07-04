<?php

namespace App\Controller;

use App\Repository\QcmInstanceRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index( StudentRepository $studentRepo, QcmInstanceRepository $qcmInstanceRepo): Response
    {
        $student = $studentRepo->find( 12345 ); // changer l'id pour l'id de l'etudiant qui est log

        // Recupérer l'instance de QCM pour laquelle la date du jour se trouve entre release_date et end_date pour l'etudiant connecté


        // Recupérer les de QCM ayant is_official false/0

        // Recupérer tous les modules (voir pour resteindre à ce qu'il étudie ou non ?)

        // Recupérer tous les QCM de la table result pour l'id de l'etudiant

        // Recupérer tous les QCM de la table result pour l'id de l'etudiant qui ont un total score < 50

        return $this->render('student/index.html.twig', [
            'student' => $student
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