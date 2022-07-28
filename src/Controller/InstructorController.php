<?php

namespace App\Controller;


use App\Entity\Proposal;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Form\QuestionType;
use App\Repository\ProposalRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class InstructorController extends AbstractController
{
    private $repository;
    private $proposals;
    private $qcm;

    public function __construct(QuestionRepository $repository, ProposalRepository $proposals,QcmRepository $qcm){
        $this->repository = $repository;
        $this->proposals = $proposals;
        $this->qcm =$qcm;
    }

    /**
     * @Route("instructor/questions/{id}/", name="instructor_display_questions")
     * @return Response
     */
    public function displayQuestions(ManagerRegistry $doctrine,int $id): Response
    {


        //  // Test Display Questions By Qcm

        $qcms= $doctrine->getRepository(Qcm::class)->findAll();
        $questionEssaie1=count( $qcms[0]->getQuestions());
        $arrayQuestion=$qcms[0]->getQuestions();
        $lengthQcm=count($qcms);
        dump($lengthQcm,'ici');
        dump($doctrine->getRepository(Qcm::class));

        // Version correct
        $qcmById = $this->qcm->findByQcmId($id);
        $d = $qcmById->getQuestions()[0]->getWording();
        $questionByQcm=$qcmById->getQuestions();
        // dump($d);

        for($essaie = 0; $essaie < $questionEssaie1;$essaie++){
              $youpiTest=$arrayQuestion[$essaie];
              $questionList=$arrayQuestion;
        //    dump($youpiTest);
        }

        // dd('la');

        // Display Questions and Answers //

        $proposals = [];
        $proposalValues =[];
        $resumeProposal = [];
        // $questions = $this->repository->findBy(['id_author' => 2]);
        // foreach ($questions as $question) {
        //     $question_id = $question->getId();
        //     $proposals[$question_id] = $this->proposals->findBy(['question' => $question_id]);
        //     foreach ($proposals[$question_id] as $proposal){
        //         $proposalValues = [
        //             'id'=>$proposal->getId(),
        //             'wording'=>$proposal->getWording(),
        //             'id_question'=>$proposal->getQuestion()->getId()
        //         ];
        //         array_push($resumeProposal, $proposalValues);
        //     }

         

       

        return $this->render('instructor/index.html.twig', [ 
            // 'questions' => $questions,
            // 'proposals' => $resumeProposal,
            'qcm'=>$qcms,
            'ok'=>$questionList,
            'questionByQcm'=>$questionByQcm,
            'qcmById'=>$qcmById
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
        foreach ($instanceQuestion->getProposals() as $beforeProp){
            array_push($arrayBeforeProp, $beforeProp->getId());
        }

        // création form
        $form = $this->createForm(CreateQuestionType::class,$instanceQuestion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $count = 0;
            $persitPropCount=0;
            $persitProp = [];
            foreach ($instanceQuestion->getProposals() as $prop){
                $bool = in_array($prop->getId(),$arrayBeforeProp);
                // Si la prop est une déjà créer en db ou si son id est null alors si elle vient d'être créée.
                    if($bool || $prop->getId() == null ){

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

            $this->addFlash('success', 'La question a bien été modifiée.');
            return $this->redirectToRoute('instructor_display_questions');
        }

        return $this->render('instructor/modify_question.html.twig', [
            'form' => $form->createView(),
            'distribute' => $distribute,
            'session' => $session,
            "add"=>false,
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

        $proposal1 = new Proposal();
        $proposal1->setWording('');

        $proposal2 = new Proposal();
        $proposal2->setWording('');

        $questionEntity->addProposal($proposal2);
        $questionEntity->addProposal($proposal1);

        // création form
        $form = $this->createForm(CreateQuestionType::class,$questionEntity);
        // accès aux données du form
        $form->handleRequest($request);

        // vérification des données soumises
        if($form->isSubmitted() && $form->isValid()){
//            dd('submited');
            $count = 0;
            $persitPropCount=0;
            foreach ($questionEntity->getProposals() as $proposal){

                // set les proposals
                $proposal->setQuestion($questionEntity);
                $persitPropCount ++;

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

        return $this->render('instructor/create_question.html.twig', [
            'form' => $form->createView(),
            "add"=>true,
        ]);
    }
}


