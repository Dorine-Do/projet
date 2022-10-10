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
    #[Route('/bug/report', name: 'app_bug_report', methods: ['POST'])]
    public function ajaxBugReport( Request $request, UserRepository $userRepo, EntityManagerInterface $manager): JsonResponse
    {
        $bugReporter = $request->request->get('bugReportUserId');
        $bugReportUrl = $request->request->get('bugReportUrl');
        $reportBugMsg = $request->request->get('reportBugMsg');

        $user = $userRepo->find( $bugReporter );

        $bugReport = new BugReport();
        $bugReport->setUser($user);
        $bugReport->setMessage( 'Bug signalé sur la page ' . $bugReportUrl . ' : ' . $reportBugMsg );
        $bugReport->setCreatedAt( new \DateTime() );

        $manager->persist($bugReport);
        $manager->flush();

        return $this->json( 'Merci, le bug a bien été signalé', 200 );
    }
}
