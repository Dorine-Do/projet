<?php

namespace App\Controller;


use App\Entity\Proposal;
use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Helpers\QcmHelper;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class InstructorController extends AbstractController
{
//    TODO future page à implémenter
    #[Route('/instructor', name: 'app_instructor')]
    public function index(): Response
    {
        return $this->render('instructor/index.html.twig', []);
    }

    #[Route('instructor/questions', name: 'instructor_display_questions', methods: ['GET'])]
    public function displayQuestions(QuestionRepository $questionRepository, ProposalRepository $proposalRepository): Response
    {
        $proposals = [];
        $resumeProposal = [];

        $questions = $questionRepository->findBy(['author' => 2]);
        foreach ($questions as $question) {
            $question_id = $question->getId();
            $proposals[$question_id] = $proposalRepository->findBy(['question' => $question_id]);
            foreach ($proposals[$question_id] as $proposal){
                $proposalValues = [
                    'id'=>$proposal->getId(),
                    'wording'=>$proposal->getWording(),
                    'id_question'=>$proposal->getQuestion()->getId()
                ];
                array_push($resumeProposal, $proposalValues);
            }
        }
        return $this->render('instructor/display_questions.html.twig', [
            'questions' => $questions,
            'proposals' => $resumeProposal,
        ]);
    }

    #[Route('instructor/questions/modify_question/{question}', name: 'instructor_modify_question', methods: ['GET', 'POST'])]
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
                if($prop->getIsCorrectAnswer() === true){
                    $count++;
                }
            }

            // Set le champs ResponseType
            if($count > 1){
                $instanceQuestion->setIsMultiple(true);
            }elseif ($count == 1){
                $instanceQuestion->setIsMultiple(false);
            }

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

    #[Route('instructor/questions/create_question', name: 'instructor_create_question', methods: ['GET', 'POST'])]
    public function createQuestion(Request $request, EntityManagerInterface $em, InstructorRepository $instructorRepository): Response
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
            $count = 0;
            $persitPropCount=0;
            foreach ($questionEntity->getProposals() as $proposal){

                // set les proposals
                $proposal->setQuestion($questionEntity);
                $persitPropCount ++;

                // set le response type
                if($proposal->getIsCorrectAnswer() === true){
                    $count++;
                }
            }
            if($count > 1){
                $questionEntity->setIsMultiple("true");
            }elseif ($count == 1){
                $questionEntity->setIsMultiple("false");
            }

            /*TODO Devra être automatisé avec l'id du user connecté si id appartient à un admin alors Null si appartient à un instructor alors id*/

            $questionEntity->setAuthor($instructorRepository->find(2));
            $questionEntity->setIsOfficial(false);
            $questionEntity->setIsMandatory(false);
            $questionEntity->setExplanation('Explication');

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

    #[Route('instructor/qcms/create_qcm_perso', name: 'instructor_create_qcm_perso', methods: ['GET', 'POST'])]
    public function createQcmPersonalized(InstructorRepository $instructorRepository, ModuleRepository $moduleRepository, QuestionRepository $questionRepository, UserRepository $userRepository, Security $security, EntityManagerInterface $entityManager){

        $linksInstructorSessionModule = $instructorRepository->find(5)->getLinksInstructorSessionModule();
        $modules = [];
        $module = null;
        foreach ($linksInstructorSessionModule as $linkInstructorSessionModule){
            $modules[]=$linkInstructorSessionModule->getModule()->getTitle();
            $module = $linkInstructorSessionModule->getModule();
        }

        $qcmGenerator = new QcmHelper($questionRepository, $userRepository, $security, $entityManager);
        $generatedQcm = $qcmGenerator->generateRandomQcm($module,true,2);
//        dd($generatedQcm);



        return $this->render('instructor/create_qcm_perso.html.twig', [
            'modules' => $modules
        ]);

    }
}