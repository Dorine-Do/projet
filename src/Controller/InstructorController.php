<?php

namespace App\Controller;


use App\Entity\Module;
use App\Entity\Proposal;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Helpers\QcmHelper;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProposalRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("instructor{idAuthor}/questions/{id}/", name="instructor_display_questions")
     * @return Response
     */
    public function displayQuestions(ManagerRegistry $doctrine,int $id,int $idAuthor): Response
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
        // $d = $qcmById->getQuestions()[0]->getWording();//test
        // $questionByQcm=$qcmById->getQuestions();
        // dump($d);

        $id_author=$this->qcm->findByQcmIdAuthor($idAuthor);

        if($id_author->getId() == $id){
            $questionByQcm=$qcmById->getQuestions();


        }
        else{
            throw new NotFoundHttpException('Erreuuuuuuuuuuuuur');
        }
        //  dd($qcmById->getQuestions());
        // dump($id_author->getId());
        //  dd($qcmById);
        //test for pour afficher dans le dump les questions
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
            'qcmById'=>$qcmById,
            'idAuthor'=>$id_author
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

    #[Route('instructor/qcms/create_qcm_perso', name: 'instructor_create_qcm_perso', methods: ['GET'])]
    public function createQcmPersonalized(Request $request, InstructorRepository $instructorRepository, ModuleRepository $moduleRepository, QuestionRepository $questionRepository, UserRepository $userRepository, Security $security){

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
            dump($module);
            $qcmGenerator = new QcmHelper($questionRepository, $userRepository, $security);
            $generatedQcm = $qcmGenerator->generateRandomQcm($module);
            $customQuestions = $questionRepository->findBy(['isOfficial' => false, 'isMandatory' => false, 'module'=> $module->getId(), 'author'=> $userId ]);
            $officialQuestions = $questionRepository->findBy(['isOfficial' => true, 'isMandatory' => false, 'module'=> $module->getId() ]);

//            dd($customQuestions);
        }
        return $this->render('instructor/create_qcm_perso.html.twig', [
            'modules' => $modules,
            'generatedQcm' => $module ? $generatedQcm : null,
            'customQuestions' => $module ? $customQuestions : null,
            'officialQuestions' => $module ? $officialQuestions : null
        ]);

    }
}