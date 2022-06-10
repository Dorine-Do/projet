<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/{reactRouting}", priority="-1" , name="home", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/instucteur/questions", name="display_instructeur_questions")
     */
    public function getUsers(QuestionRepository $questionRepository)
    {
        $questions = $questionRepository->find(2);
        foreach ($questions as $key => $question){
            $questions[$key] = (array)$question;
        }
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode($questions));

        return $response;
    }
}
