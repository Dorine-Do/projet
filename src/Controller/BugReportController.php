<?php

namespace App\Controller;

use App\Entity\Main\BugReport;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BugReportController extends AbstractController
{
    #[Route('/bug/report/', name: 'app_bug_report', methods: ['POST'])]
    public function ajaxBugReport( Request $request, UserRepository $userRepo, EntityManagerInterface $manager ): JsonResponse
    {
        $ajaxContent = $request->getContent();
        dd(json_decode($ajaxContent));
//        $user = $userRepo->find( $ajaxContent['bugReporter'] );
//        $bugReport = new BugReport();
//        $bugReport->setUser($user);
//        $bugReport->setMessage( 'Bug signalé sur: ' . $ajaxContent['bugUrl'] . ' : ' . $ajaxContent['bugMsg'] );
//        $bugReport->setCreatedAt( new \DateTime() );
//
//        $manager->persist($bugReport);
//        $manager->flush();
        // $reporter = $request->get('bugReporter');
        return $this->json( $ajaxContent, 200 );
    }
}
