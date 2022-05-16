<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    #[Route('/question', name: 'app_question')]

    private $repository;

    public function __construct(QuestionRepository $repository){
        $this->repository = $repository;
    }

//    test

    /**
     * @Route("/quest", name="question.index")
     * @return Response
     */
    public function index(): Response

    {
        $question = $this->repository->findALlVisible();
        dd($question);
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }
}
