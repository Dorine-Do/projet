<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Proposal;
use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Entity\Session;
use App\Form\CreateQuestionType;
use App\Helpers\QcmGeneratorHelper;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\ProposalRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class InstructorController extends AbstractController
{
// voir create qcm
    Const dayOfWeek = [
        0 => 'Monday',
        1 => 'Thuesday',
        2 => 'Wednesday',
        3 => 'Thusday',
        4 => 'Friday',
        5 => 'Saturday',
        6 => 'Sunday'
    ];
    const monthOfYear =[
        0 => 'January',
        1 => 'February',
        2 => 'March',
        3 => 'April',
        4 => 'May',
        5 => 'June',
        6 => 'July',
        7 => 'August',
        8 => 'September',
        9 => 'October',
        10 => 'November',
        11 => 'December',
        
    ];
//    TODO future page à implémenter
    #[Route('/instructor', name: 'welcome_instructor')]
    public function welcome(): Response
    {
        return $this->render('instructor/welcome_instructor.html.twig', []);
    }

    #[Route('instructor/questions', name: 'instructor_display_questions', methods: ['GET'])]
    public function displayQuestions(QuestionRepository $questionRepository, ProposalRepository $proposalRepository): Response
    {
        $proposals = [];
        $resumeProposal = [];

        $questions = $questionRepository->findBy(['author' => $this->getUser()->getId()]);
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
        $date = $releasedate[0]['startTime'];
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

    #[Route('instructor/qcms', name: 'instructor_qcms', methods: ['GET'])]
    public function displayQcms( QcmRepository $qcmRepo, Security $security): Response
    {
        $qcms = $qcmRepo->findBy([
            'author' => $security->getUser(),
        ]);

        return $this->render('instructor/display_qcms.html.twig', [
            'qcms' => $qcms
        ]);
    }

    /**TODO
     * à l'aide du generatorHelper generer un qcm qui sera enregistrer dans la bdd table qcm puis 
     * qui génera un instance de qcm automatique pour chaque élève en recupérant le start time et le end time dans linksessionmodule
     * 
     * Déclencher la fonction du générateur au clic
     */
    #[Route('instructor/create-official-qcm',name:'instructor_create_qcm',methods:['GET','POST'])]
    public function createOfficialQcm(Security $security,SessionRepository $sessionRepository,InstructorRepository $instructorRepository,Request $request,QuestionRepository $questionRepository,ModuleRepository $moduleRepository,EntityManagerInterface $manager): Response
    {

      
        $userId=$security->getUser();
        $sessionAndModuleByInstructor= $instructorRepository->find($userId)->getLinksInstructorSessionModule();
       

        //   $dateToday = new \DateTime();
        //   $dateFormat = date_format($dateToday,'Y-m-d H:i:s');
        //   if($dateFormat){
        //       //date(h:i:s)
        //       // après avoir fait le date interval 
        //       // recupérer le valeur et le format precis et remplacer l'heure
        //       //datetime(17:00:00)metrre en format his et comparer avec la date de l'inetrvale
        //       $endDate=$dateToday->add(new DateInterval("P5D"));
        //       $endDateFormat=date_format($endDate,'Y-m-d 17:00:00');
        //       $format= $endDateFormat;
        //     //   dd($endDateFormat);
        //     dump($endDateFormat);
        //       $date1 = date("Y-m-d 17:00:00", strtotime($endDateFormat.'+ 1 days'));
             
        //       dd($date1);
        //   }
       
       
        
        foreach ($sessionAndModuleByInstructor as $sessionAndModuleByInstructor){
            $sessionId=$sessionAndModuleByInstructor->getSession()->getId();
            $moduleId=$sessionAndModuleByInstructor->getModule()->getId();
            $sessions=$sessionRepository->findBy(['id'=>$sessionId]);
            $modules=$sessionRepository->findBy(['id'=>$moduleId]);
            // $sessions=array($sessionAndModuleByInstructor->getSession());
           
        }
       
       
        $formData= $request->query->all();
        if(count($formData) != 0 ){

        $module=$moduleRepository->find($formData["module"]);
        $qcmGenerator = new QcmGeneratorHelper($questionRepository, $security);
        $qcm=$qcmGenerator->generateRandomQcm($module,false);
        $manager->persist($qcm);
        $manager->flush();

        // dd($qcm);

        $linksSessionStudent=$sessionRepository->find($formData["session"])->getLinksSessionStudent();
        $students=[];
        foreach($linksSessionStudent as $linkSessionStudent){
            // dd($linkSessionStudent);

            $students[]=$linkSessionStudent->getStudent();
        }
        foreach($students as $student){
            $qcmInstance= new QcmInstance();
            $qcmInstance->setStudent($student) ;
            $qcmInstance->setQcm($qcm);
            $qcmInstance->setCreatedAtValue();
            $qcmInstance->setUpdateAtValue();

            //START TIME AND END TIME 
            $dayOfCreationOfQcmInstance=$qcmInstance->getCreatedAt();
            if($dayOfCreationOfQcmInstance){
             $dateOfCreationFormat=date_format($dayOfCreationOfQcmInstance, "Y-m-d H:i:s");
             $newDateTimeForStartTime=date("Y-m-d 13:00:00", strtotime($dateOfCreationFormat.'+ 5 days'));
             $startTime=new \DateTime($newDateTimeForStartTime);
             $qcmInstance->setStartTime($startTime);
             $newDateTimeForEndTime=date("Y-m-d H:i:s",strtotime($newDateTimeForStartTime.'+ 4hours'));
             $endTime=new \Datetime($newDateTimeForEndTime);
             $qcmInstance->setEndTime($endTime);
            
              //on recupère on crée une variable dans lequel on met le string de getcreatedat ,
              //puis on place la varible dans datetime puis on lei donne un format et ainsi de suite  
            }
           
            // dd($qcmInstance);
         
            $manager->persist($qcmInstance);
            $manager->flush();

       

            $this->addFlash(
                'instructorAddQcm',
                'Le qcm a été généré avec succès'
            );
            return $this->redirectToRoute('welcome_instructor');
            
        }
    //  redirect to route avec flash vers welcome instructor
           
        }

      
       return $this->render('instructor/create_official_qcm.html.twig',[
           'sessions'=>$sessions,
           'modules'=>$modules
       ]);
    }
    #[Route('instructor/qcms/test',name:'test',methods:['GET','POST'])]
    public function test():Response
    {
        return $this->render('instructor/test.html.twig');
    }
}

