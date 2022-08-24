<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnectionController extends AbstractController
{
//    #[Route('/', name: 'app_connection')]
//    public function index( AuthenticationUtils $authUtils ): Response
//    {
//        $error = $authUtils->getLastAuthenticationError();
//        $lastUsername = $authUtils->getLastUsername();
//
//        return $this->render('connection/index.html.twig', [
//            'last_username' => $lastUsername,
//            'error' => $error
//        ]);
//    }

//    #[Route('/dashboard/check', name: 'app_check_dashboard')]
//    public function roleChecking( Security $security): Response
//    {
//        $user = $security->getUser();
//
//        $userRoles = $user->getRoles();
//
//        if( in_array('ROLE_ADMIN', $userRoles) )
//        {
//            $dashboardRouteName = 'app_admin';
//        }
//        elseif( in_array('ROLE_INSTRUCTOR', $userRoles) )
//        {
//            $dashboardRouteName = 'welcome_instructor';
//        }
//        elseif( in_array('ROLE_STUDENT', $userRoles) )
//        {
//            $dashboardRouteName = 'student_qcms';
//        }
//        elseif( in_array('ROLE_USER', $userRoles) )
//        {
//            $dashboardRouteName = 'home';
//        }
//
//        return $this->redirectToRoute( $dashboardRouteName );
//    }
}
