<?php

namespace App\Controller;


use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Form\QuestionType;
use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("instructor/questions", name="instructor_display_questions")
     * @return Response
     */
    public function displayQuestions(): Response
    {
        $proposals = [];
        $proposalValues =[];
        $resumeProposal = [];
        $questions = $this->repository->findBy(['id_author' => 2]);
        foreach ($questions as $question) {
            $question_id = $question->getId();
            $proposals[$question_id] = $this->proposals->findBy(['question' => $question_id]);
            foreach ($proposals[$question_id] as $proposal){
                $proposalValues = [
                    'id'=>$proposal->getId(),
                    'wording'=>$proposal->getWording(),
                    'id_question'=>$proposal->getQuestion()->getId()
                ];
                array_push($resumeProposal, $proposalValues);
            }
        }
//        dd($resumeProposal);
        return $this->render('instructor/index.html.twig', [
            'questions' => $questions,
            'proposals' => $resumeProposal,
        ]);
    }

    /**
     * @Route("instructor/modify_question/{question}", name="instructor_modify_question")
     * @param Request $request
     * @param $em
     * @return Response
     */
    public function modifyQuestion(Request $request, Question $question, EntityManagerInterface $em): Response
    {
        $questionEntity= new Question();
        // création form
        $form = $this->createForm(QuestionType::class,$question);
        // voir createdAt et UpdatedAt
        $date=new \DateTime();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $count = 0;
            foreach ($question->getProposal() as $prop){
                if($prop->getIsCorrect() === true){
                    $count++;
                }
            }
            if($count > 1){
                $question->setResponseType("checkbox");
            }elseif ($count = 1){
                $question->setResponseType("radio");
            }
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('display_questions');
        }


        return $this->render('instructor/modify_question.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("instructor/create_question", name="instructor_create_question")
     * @return Response
     * @param $em
     */
    public function createQuestion(Request $request, EntityManagerInterface $em): Response
    {
        $questionEntity= new Question();
        // création form
        $form = $this->createForm(CreateQuestionType::class,$questionEntity);
        // accès aux données du form
        $form->handleRequest($request);
        // voir createAt et UpdatedAt -> voir construct dans question
        // $date=new \DateTime();

        // vérification des données soumises
        if($form->isSubmitted() && $form->isValid()){

            $count = 0;
            foreach ($questionEntity->getProposal() as $proposal){
                // set les proposals
                $proposal->setQuestion($questionEntity);
                // set le response type
                if($proposal->getIsCorrect() === true){
                    $count++;
                }
            }
            if($count > 1){
                $questionEntity->setResponseType("checkbox");
            }elseif ($count == 1){
                $questionEntity->setResponseType("radio");
            }

            $questionEntity->setIdAuthor(2);
            $questionData=$form->getData();
            // $questionData->setCreatedAt($date); // Pas necessaire car créer directement dans le construct de l'entity Question
            // $questionData->setUpdatedAt($date); // Pas necessaire car créer directement dans le construct de l'entity Question
            $questionData->setIsOfficial(false);// Toujours false quand c'est un instructor qui créé une question
            /* TODO setDifficulty avec Enum */
            //  $questionData->setDifficulty(true);//temporaire
            /* TODO setResponseType */
            $questionData->setResponseType(true);//temporaire => Voir dans addQuestion
            $questionData->setIsMandatory(false);// Toujours false quand c'est un instructor qui créé une question
//            dd($form->getData());

            /* TODO faire le insert des datas des reponses*/

            //  validation et enregistrement des données du form dans la bdd
            $em->persist($questionEntity);
            $em->flush();
        }

        return $this->render('instructor/create_question2.html.twig', [
            'controller_name' => 'InstructorController',
            'form' => $form->createView(),

        ]);
    }
}