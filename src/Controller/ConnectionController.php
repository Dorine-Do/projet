<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnectionController extends AbstractController
{
    #[Route('/connection', name: 'app_connection')]
    public function index( AuthenticationUtils $authUtils ): Response
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('connection/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


}
