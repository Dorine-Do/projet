<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QcmAdministrationController extends AbstractController
{
    #[Route('/qcm/administration', name: 'app_qcm_administration')]
    public function index(): Response
    {
        return $this->render('qcm_administration/index.html.twig', [
            'controller_name' => 'QcmAdministrationController',
        ]);
    }
}
