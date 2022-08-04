<?php

namespace App\Controller;

use App\Entity\Proposal;
use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Helpers\QcmGeneratorHelper;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProposalRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InstructorController extends AbstractController
{
//    TODO future page à implémenter
    #[Route('/instructor', name: 'welcome_instructor')]
    public function welcome(): Response
    {
        return $this->render('instructor/welcome_instructor.html.twig', []);
    }

    #[Route('instructor/questions', name: 'instructor_display_questions', methods: ['GET'])]
    public function displayQuestions(
        QuestionRepository $questionRepository,
        ProposalRepository $proposalRepository
    ): Response
    {
        $proposals = [];
        $resumeProposal = [];

        $questions = $questionRepository->findBy( [ 'author' => $this->getUser()->getId() ] );
        foreach( $questions as $question )
        {
            $question_id = $question->getId();
            $proposals[$question_id] = $proposalRepository->findBy( ['question' => $question_id] );
            foreach( $proposals[$question_id] as $proposal )
            {
                $proposalValues = [
                    'id'=>$proposal->getId(),
                    'wording'=>$proposal->getWording(),
                    'id_question'=>$proposal->getQuestion()->getId()
                ];
                $resumeProposal[] = $proposalValues;
            }
        }
        return $this->render('instructor/display_questions.html.twig', [
            'questions' => $questions,
            'proposals' => $resumeProposal,
        ]);
    }

    #[Route('instructor/questions/modify_question/{question}', name: 'instructor_modify_question', methods: ['GET', 'POST'])]
    public function modifyQuestion(
        Request $request,
        Question $question,
        QuestionRepository $questionRepository,
        ProposalRepository $proposalRepository,
        EntityManagerInterface $manager
    ): Response
    {
        $releaseDateOnSession = $questionRepository->getSessionWithReleaseDate($question);
        if( $releaseDateOnSession )
        {
            $session = $releaseDateOnSession[0]['name'];
        }
        else
        {
            $session = null;
        }

        // GetQuestionById with release_date
        $releaseDate = $questionRepository->getQuestionWithReleaseDate($question);

        if( $releaseDate )
        {
            $date = $releaseDate[0]['startTime'];
            $distribute = date_format($date, 'd/m/y');
        }
        else
        {
            $distribute = null;
        }

        // GetQuestionById
        $instanceQuestion = $questionRepository->find($question);

        //Stock les id avant render le form
        $arrayBeforeProp = [];
        foreach( $instanceQuestion->getProposals() as $beforeProp )
        {
            $arrayBeforeProp[] = $beforeProp->getId();
        }

        // création form
        $form = $this->createForm(CreateQuestionType::class, $instanceQuestion );

        $form->handleRequest( $request );

        if( $form->isSubmitted() && $form->isValid() )
        {
            $count = 0;
            $persistPropCount=0;
            $persistProp = [];
            foreach( $instanceQuestion->getProposals() as $prop )
            {
                $bool = in_array($prop->getId(),$arrayBeforeProp);
                // Si la prop est une déjà créer en db ou si son id est null alors si elle vient d'être créée.
                if( $bool || $prop->getId() == null )
                {
                    // Si l'utilisateur a modifié la reponse
                    $prop->setQuestion($instanceQuestion);
                    $persistProp[] = $prop->getId();
                    $persistPropCount++;
                }
                // Si la reponse est une reponse correcte
                if( $prop->getIsCorrectAnswer() )
                {
                    $count++;
                }
            }

            // Set le champs ResponseType
            if($count > 1)
            {
                $instanceQuestion->setIsMultiple(true);
            }
            elseif( $count == 1 )
            {
                $instanceQuestion->setIsMultiple(false);
            }

            //Supprime le lien entre les proposals et la question que l'utilisateur ne veut plus
            $removeProp = array_diff($arrayBeforeProp,$persistProp);
            foreach( $removeProp as $id )
            {
                $prop = $proposalRepository->find($id);
                $manager->remove($prop);
            }

            $manager->persist( $instanceQuestion );
            $manager->flush();

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
    public function createQuestion(
        Request $request,
        EntityManagerInterface $manager,
    ): Response
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
        if($form->isSubmitted() && $form->isValid())
        {
            $count = 0;
            $persitPropCount=0;
            foreach ($questionEntity->getProposals() as $proposal)
            {
                // set les proposals
                $proposal->setQuestion($questionEntity);
                $persitPropCount ++;

                // set le response type
                if($proposal->getIsCorrectAnswer() === true)
                {
                    $count++;
                }
            }
            if($count > 1)
            {
                $questionEntity->setIsMultiple("true");
            }
            elseif ($count == 1)
            {
                $questionEntity->setIsMultiple("false");
            }

            $questionEntity->setAuthor( $this->getUser() );
            $questionEntity->setIsOfficial(false);
            $questionEntity->setIsMandatory(false);
            $questionEntity->setExplanation('Explication');
//            dd();
            $questionEntity->setDifficulty(intval($form->get('difficulty')->getViewData()));

            //  validation et enregistrement des données du form dans la bdd
            $manager->persist($questionEntity);
            $manager->flush();

            return $this->redirectToRoute('instructor_display_questions');
        }

        return $this->render('instructor/create_question.html.twig', [
            'form' => $form->createView(),
            "add"=>true,
        ]);
    }

    #[Route('instructor/qcms/create_qcm_perso', name: 'instructor_create_qcm_perso', methods: ['GET','POST'])]
    public function createQcmPersonalized(
        Request $request,
        InstructorRepository $instructorRepository,
        ModuleRepository $moduleRepository,
        QuestionRepository $questionRepository,
        Security $security
    ): Response
    {

        $userId = $this->getUser()->getId();
        $linksInstructorSessionModule = $instructorRepository->find($userId)->getLinksInstructorSessionModule();

        $modules = [];
        foreach ($linksInstructorSessionModule as $linkInstructorSessionModule){
            $modules[]=$linkInstructorSessionModule->getModule();
        }
        $module = null;
        if( $request->get('module') ){
            $module = $moduleRepository->find($request->get('module'));
        }

        if ($module){
            $qcmGenerator = new QcmGeneratorHelper($questionRepository, $security);
            $generatedQcm = $qcmGenerator->generateRandomQcm($module);
            $customQuestions = $questionRepository->findBy(['isOfficial' => false, 'isMandatory' => false, 'module'=> $module->getId(), 'author'=> $userId ]);
            $officialQuestions = $questionRepository->findBy(['isOfficial' => true, 'isMandatory' => false, 'module'=> $module->getId() ]);
        }
        return $this->render('instructor/create_qcm_perso.html.twig', [
            'modules' => $modules,
            'generatedQcm' => $module ? $generatedQcm : null,
            'customQuestions' => $module ? $customQuestions : null,
            'officialQuestions' => $module ? $officialQuestions : null
        ]);

    }

    #[Route('instructor/questions/upDateFetch', name: 'instructor_questions_upDateFetch', methods: ['POST'])]
    public function upDateQuestionFetch(ValidatorInterface $validator): Response
    {
        $values = $_POST;
        $question = new Question();

        return new JsonResponse('ok');
    }

    #[Route('instructor/qcms', name: 'instructor_qcms', methods: ['GET'])]
    public function displayQcms(
        QcmRepository $qcmRepo,
        Security $security
    ): Response
    {
        $qcms = $qcmRepo->findBy([
            'author' => $security->getUser(),
        ]);

        return $this->render('instructor/display_qcms.html.twig', [
            'qcms' => $qcms
        ]);
    }

    #[Route('instructor/plan_qcm/', name: 'instructor_plan_qcm')]
    public function planQcm()
    {
        $instructor = $this->getUser();

        return $this->render( 'instructor/plan_qcm.html.twig', []);
    }
}