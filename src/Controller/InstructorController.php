<?php

namespace App\Controller;


use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InstructorController extends AbstractController
{
    private $repository;
    private $proposals;

    public function __construct(QuestionRepository $repository, ProposalRepository $proposals){
        $this->repository = $repository;
        $this->proposals = $proposals;
    }

//    TODO future page à implémenter
//    #[Route('/instructor', name: 'app_instructor')]
//    public function index(): Response
//    {
//        return $this->render('instructor/index.html.twig', [
//            'controller_name' => 'InstructorController',
//        ]);
//    }

    /**
     * @Route("/questions", name="display_questions")
     * @return Response
     */
    public function displayQuestions(): Response
    {
        $proposals = [];
        $questions = $this->repository->findBy(['id_author' => 2]);

        foreach ($questions as $question) {

            $question_id = $question->getId();
            $proposals[$question_id] = $this->proposals->findBy(['question' => $question_id]);
        }
        return $this->render('instructor/index.html.twig', [
            'questions' => $questions,
            'proposals' => $proposals,
        ]);
    }

    /**
     * @Route("/modify_question", name="modify.question")
     * @return Response
     */
    public function modifyQuestion(): Response
    {
        $question_id = 1;
        $instructor_id = 1;
        $question = $this->repository->findQuestionById($question_id, $instructor_id);

        return $this->render('instructor/index.html.twig', [
            'controller_name' => 'InstructorController',
        ]);
    }

    // init de branches
    // init de InstructorQuestionForm
    /**
     * @Route("/create_question", name="app_create_question")
     * @return Response
     */
    public function createQuestion(Request $request): Response

    {

        $questionEntity= new Question();
        // création form
        $form = $this->createForm(QuestionType::class,$questionEntity);
        // accès aux données du form
        $form->handleRequest($request);
        // voir createAt et UpdatedAt
        $date=new \DateTime();

        // vérification des données soumises
        if($form->isSubmitted() && $form->isValid()){

            $questionData=$form->getData();
            $questionData->setCreatedAt($date);
            $questionData->setUpdatedAt($date);
            $questionData->setIsOfficial(true);//temporaire
            dd($form->getData());

            //  validation et enregistrement des données du form dans la bdd
            $this->manager->persist($questionEntity);
            $this->manager->flush();
        }
        return $this->render('instructor/create_question.html.twig', [
            'controller_name' => 'InstructorController',
            'form' => $form->createView(),

        ]);
    }

}
