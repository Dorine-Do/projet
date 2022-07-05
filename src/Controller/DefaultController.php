<?php

namespace App\Controller;

use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

//    /**
//     * @Route("{reactRouting}", priority="-1" , name="home", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
//    */
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

 //   /**
 //    * @Route("/instucteur/questions", name="display_instructeur_questions")
 //    */
 //   public function getQuestionsByIdAuthor(QuestionRepository $questionRepository, ProposalRepository $proposalRepository)
 //   {
 //       // findBy returns an array of Instances's Entity = ARRAY
 //       // find return ONE Instance's Entity = OBJECT
//        $questions = $questionRepository->findBy(["id_author" => 2]);
//
 //       $questionOrders = [];
 //       $resumeProposal = [];
//
 //       foreach ($questions as $question) {
 //           $question_id = $question->getId();
 //           $questionOrder = [
 //               'id' => $question_id,
 //               'wording' => $question->getWording(),
 //               'difficulty' => $question->getDifficulty(),
 //               'module' => $question->getModule(),
  //              'enabled' => $question->getEnabled()
 //          ];
 //           array_push($questionOrders, $questionOrder);
//
 //           $proposals[$question_id] = $proposalRepository->findBy(['question' => $question_id]);
 //           foreach ($proposals[$question_id] as $proposal){
 //               $proposalValues = [
 //                   'id'=>$proposal->getId(),
 //                   'wording'=>$proposal->getWording(),
 //                   'id_question'=>$proposal->getQuestion()->getId()
 //               ];
  //              array_push($resumeProposal, $proposalValues);
 //           }
  //      }
//        dd($questionOrder);
 //       $proposals = ['proposals' => $resumeProposal];
 //       $questions = ['questions' => $questionOrders];
 //       $questionsProposals = array_merge($questions,$proposals);
//        dd($questionsProposals);

 //       $response = new Response();

 //       /*TODO : réparer l'erreur : Blocage d’une requête multiorigines (Cross-Origin Request) */
 //       $response->headers->set('Content-Type', 'application/json');
 //       $response->headers->set('Access-Control-Allow-Origin', '*');

//        $response->setContent(json_encode($questionsProposals));
//        $response->setContent(json_encode($resumeProposal));

//        return $response;
 //   }

}
