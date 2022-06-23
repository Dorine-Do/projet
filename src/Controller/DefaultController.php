<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("{reactRouting}", priority="-1" , name="home", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
     */
//    /**
//     * @Route("/", name="home")
//     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/instucteur/questions", name="display_instructeur_questions")
     */
    public function getQuestionsByIdAuthor(QuestionRepository $questionRepository)
    {
        // findBy returns an array of Instances's Entity = ARRAY
        // find return ONE Instance's Entity = OBJECT
        $questions = $questionRepository->findBy(["id_author" => 2]);
        $questionOrder = [];
        foreach ($questions as $key => $question){
//            echo $key;
//            dd($question);
            $informations= array(
                "id"=>$question->getId(),
                "wording"=>$question->getWording()
            );
            array_push($questionOrder, );
            $questions[$key] = array($question);
        }

        $response = new Response();

        /*TODO : réparer l'erreur : Blocage d’une requête multiorigines (Cross-Origin Request) */
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode($questions));

        return $response;
    }
}
