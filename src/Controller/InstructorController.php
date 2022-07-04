<?php

namespace App\Controller;


use App\Entity\Question;
use App\Form\CreateQuestionType;
use App\Form\QuestionType;
use App\Repository\ProposalRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Tests\IdGenerator\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @param Request $request
     * @param $em
     * @return Response
     */
    public function modifyQuestion(Request $request, EntityManagerInterface $em): Response
    {
        $questionEntity= new Question();
        // création form
        $form = $this->createForm(CreateQuestionType::class,$questionEntity);
        // voir createdAt et UpdatedAt
        $date=new \DateTime();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            dd($data['Module']);

            $data->setModule($data['Module']);
            $data->setIntitule($data['Intitule']);
            $data->setDifficulte($data['Difficulte']);
            $data->setReponses($data['Reponses']);
            $data->setReponses_correctes($data['Reponses_correctes']);
            $em->persist($data);
            $em->flush();

            return $this->redirectToRoute('questions');
        }


        return $this->render('instructor/modify_question.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // init de branches
    // init de InstructorQuestionForm
//    /**
//     * @Route("/create_question", name="question.index")
//     * @return Response
//     */
//    public function createQuestion(Request $request): Response
//
//    {
//        // dump($request);
//        $questionEntity= new Question();
//
//        // dd($questionEntity->setModuleId(1));
////        $question = $this->repository->findALlVisible();
////        $module = $this->repositoryModule->findALlVisible();
//        $form = $this->createForm(QuestionType::class,$questionEntity);
//        $form->handleRequest($request);
//        $date=new \DateTime();
//        dump($date->format('Y-m-d'));
//        // dd($re->get('module'));
//        // $form = $this->createForm(QuestionType::class);
//        // dd($questionEntity->setModuleId($question[0]['module_id']));
//        // dump($questionEntity);
//        // dd($form->get('wording')->setData($module[0]['title']));
//
//        // $form->get('wording')->setData($module[0]['title']); // il faut modif la requet du repository pour recupérer le id de module_id dans question
//        // dd($questionEntity->setModuleId(1));
//        // dd($form->get('wording'));
//
//        // dd($form->get('wording')->setData($module[0]['id']));// a modifier
//
//        // dd($form->get('module_id')->getData());
//        // dump($this->repositoryModule);
//        // $repository2= $doctrine->getRepository(Question::class);
//        // dd($module);
//        // echo "<pre>";
//        //     var_dump($form);
//        // echo "</pre>";
//
//
//        $mid=$questionEntity->setModuleId($module[0]['id']);
//        if($form->isSubmitted() && $form->isValid()){
//
//            $questionData=$form->getData();
//            $questionData->setCreatedAt($date);
//            $questionData->setUpdatedAt($date);
//            //  $mid2=$form->get('module_id')->setData($module[0]['id']);
//            dd($form->getData());
//            // dump($mid2);
//            // dd($mid);
//            $this->manager->persist($questionEntity);
//            $this->manager->flush();
//        }
//        return $this->render('instructor/index.html.twig', [
//            'controller_name' => 'InstructorController',
//            'form' => $form->createView(),
//            'question'=>$question,
//            'module'=>$module,
//        ]);
//    }
}