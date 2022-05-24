<?php

namespace App\Controller;

use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstructorController extends AbstractController
{
    private $repository;

    public function __construct(QuestionRepository $repository){
        $this->repository = $repository;
    }
//
//    #[Route('/instructor', name: 'app_instructor')]
//    public function index(): Response
//    {
//        return $this->render('instructor/index.html.twig', [
//            'controller_name' => 'InstructorController',
//        ]);
//    }

    /**
     * @Route("/quest", name="question.index")
     * @return Response
     */
    public function displayQuestions(): Response

    {
        $question = $this->repository->findALlVisible();
        dd($question);
        return $this->render('instructor/index.html.twig', [
            'controller_name' => 'InstructorController',
        ]);
    }
    /**
     * @Route("/insertquest", name="question.index")
     * @return Response
     */
    public function insertQuestions(): Response

    {
        $form = $this->createForm(QuestionType::class);
//        dd($form->createView());
        return $this->render('instructor/index.html.twig', [
            'controller_name' => 'InstructorController',
            'form' => $form->createView()
        ]);
    }

}
