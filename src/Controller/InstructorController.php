<?php

namespace App\Controller;


use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Form\QuestionType;
use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use DateTime;
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
    public function modifyQuestion(Request $request, Question $question, EntityManagerInterface $em, QuestionRepository $questionRepository): Response
    {
        // création form
        $form = $this->createForm(CreateQuestionType::class,$question);
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
            }elseif ($count == 1){
                $question->setResponseType("radio");
            }

            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('instructor_display_questions');
        }

        $date = new DateTime('2022-06-18');
        $donnees = $em->getRepository(QcmInstance::class)->findBy(['release_date' => $date]);
        dd($donnees);

        return $this->render('instructor/file_a_verifier/modify_question_new_version.html.twig', [
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

        // vérification des données soumises
        if($form->isSubmitted() && $form->isValid()){
            /* TODO setDifficulty avec Enum = Pour l'instant c'est un select mais devra être en bouton */

            $count = 0;
            foreach ($questionEntity->getProposal() as $proposal){
                // set les proposals
                $proposal->setQuestion($questionEntity);

                /* TODO setResponseType pour l'instant c'est checkbox avec la question mais devra être une question à part*/
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

            /*TODO Devra être automatisé avec l'id du user connecté si id appartient à un admin alors Null si appartient à un instructor alors id*/
            $questionEntity->setIdAuthor(2);

            // $questionData->setCreatedAt($date); Pas necessaire car créer directement dans le construct de l'entity Question
            // $questionData->setUpdatedAt($date); Pas necessaire car créer directement dans le construct de l'entity Question
            $questionEntity->setIsOfficial(false);// Toujours false quand c'est un instructor qui créé une question


            $questionEntity->setIsMandatory(false);// Toujours false quand c'est un instructor qui créé une question
//            dd($form->getData());

            //  validation et enregistrement des données du form dans la bdd
            $em->persist($questionEntity);
            $em->flush();

            return $this->redirectToRoute('instructor_display_questions');
        }

        return $this->render('instructor/file_a_verifier/create_question2.html.twig', [
            'controller_name' => 'InstructorController',
            'form' => $form->createView(),

        ]);
    }

}