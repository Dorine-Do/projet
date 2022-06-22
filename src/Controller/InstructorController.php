<?php

namespace App\Controller;


use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Form\QuestionType;
use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
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
    public function modifyQuestion(Request $request, $question,QuestionRepository $questionRepository, ProposalRepository $proposalRepository, EntityManagerInterface $em): Response
    {
        $releasedateonsession = $questionRepository -> getSessionWithReleaseDate($question);
        if($releasedateonsession != null){
        $session = $releasedateonsession[0]['name'];
        }else{
            $session = null;
        }

        // GetQuestionById with release_date
        $releasedate = $questionRepository -> getQuestionWithReleaseDate($question);

        if($releasedate != null){
        $date = $releasedate[0]['release_date'];
        $distribute = date_format($date, 'd/m/y');
        }else{
            $distribute = null;
        }

        // GetQuestionById
        $instanceQuestion = $questionRepository->find($question);

        //Stock les id avant render le form
        $arrayBeforeProp =[];
        foreach ($instanceQuestion->getProposal() as $beforeProp){
            array_push($arrayBeforeProp, $beforeProp->getId());
        }

        // création form
        $form = $this->createForm(CreateQuestionType::class,$instanceQuestion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $count = 0;
            $persitPropCount=0;
            $persitProp = [];
            foreach ($instanceQuestion->getProposal() as $prop){
                $bool = in_array($prop->getId(),$arrayBeforeProp);
                // Si la prop est une déjà créer en db ou si son id est null alors si elle vient d'être créée.
                    if($bool || $prop->getId() == null ){

                        // Ajout des lettres dans la réponse
                        $alphabet = ['A','B','C','D','E','F'];
                        $wording = $prop->getWording();
                        $prop->setWording($alphabet[$persitPropCount]." ".$wording);

                        // Si l'utilisateur a modifié la reponse
                        $prop->setQuestion($instanceQuestion);;
                        array_push($persitProp,$prop->getId());
                        $persitPropCount++;
                    }


                // Si la reponse est une reponse correcte
                if($prop->getIsCorrect() === true){
                    $count++;
                }
            }

            // Set le champs ResponseType
            if($count > 1){
                $instanceQuestion->setResponseType("checkbox");
            }elseif ($count == 1){
                $instanceQuestion->setResponseType("radio");
            }

            /*TODO à faire vérifier au chef => remove()*/
            //Supprime le lien entre les proposals et la question que l'utilisateur ne veut plus
            $removeProp = array_diff($arrayBeforeProp,$persitProp);
            foreach ($removeProp as $id){
                $prop = $proposalRepository->find($id);
                $em->remove($prop);
            }

            $em->persist($instanceQuestion);
            $em->flush();

            return $this->redirectToRoute('instructor_display_questions');
        }

        return $this->render('instructor/file_a_verifier/modify_question_new_version.html.twig', [
            'form' => $form->createView(),
            'distribute' => $distribute,
            'session' => $session,
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
            $persitPropCount=0;
            foreach ($questionEntity->getProposal() as $proposal){
                // Ajout des lettres dans la réponse
                $alphabet = ['A','B','C','D','E','F'];
                $wording = $proposal->getWording();
                $proposal->setWording($alphabet[$persitPropCount]." ".$wording);

                // set les proposals
                $proposal->setQuestion($questionEntity);
                $persitPropCount ++;

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