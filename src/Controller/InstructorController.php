<?php

namespace App\Controller;

    use App\Entity\Enum\Level;
    use App\Entity\Main\Instructor;
    use App\Entity\Main\Module;
    use App\Entity\Main\Proposal;
    use App\Entity\Main\Qcm;
    use App\Entity\Main\QcmInstance;
    use App\Entity\Main\Question;
    use App\Entity\Main\Result;
    use App\Entity\Main\Session;
    use App\Entity\Main\Student;
    use App\Form\CreateQuestionType;
    use App\Helpers\QcmGeneratorHelper;
    use App\Repository\InstructorRepository;
    use App\Repository\LinkInstructorSessionModuleRepository;
    use App\Repository\LinkSessionStudentRepository;
    use App\Repository\ModuleRepository;
    use App\Repository\ProposalRepository;
    use App\Repository\QcmInstanceRepository;
    use App\Repository\QcmRepository;
    use App\Repository\QuestionRepository;
    use App\Repository\ResultRepository;
    use App\Repository\SessionRepository;
    use App\Repository\StudentRepository;
    use DateInterval;
    use Doctrine\ORM\EntityManagerInterface;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Security;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class InstructorController extends AbstractController
    {
        /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
        private $id = 1;

//    TODO future page à implémenter
    #[Route('/instructor', name: 'welcome_instructor')]
    public function welcome(): Response
    {
        return $this->render('instructor/welcome_instructor.html.twig', []);
    }

        #[Route('instructor/creations',name:'my_creations',methods:['GET','POST'])]
        public function displayInstructionCreations():Response
        {
            return $this->render('instructor/my_creations.html.twig');
        }

        #[Route('instructor/creations/questions', name: 'instructor_display_questions', methods: ['GET'])]
        public function displayQuestions(
            QuestionRepository $questionRepository,
            ProposalRepository $proposalRepository
        ): Response
        {
            $proposals = [];
            $resumeProposal = [];

            /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
            $questions = $questionRepository->findBy(['author' => $this->id]);
            foreach( $questions as $question )
            {
                $question_id = $question->getId();
                $proposals[$question_id] = $proposalRepository->findBy( ['question' => $question_id] );
                foreach( $proposals[$question_id] as $proposal )
                {
                    $proposalValues = [
                        'id' => $proposal->getId(),
                        'wording' => $proposal->getWording(),
                        'id_question' => $proposal->getQuestion()->getId()
                    ];
                    $resumeProposal[] = $proposalValues;
                }
            }
            return $this->render('instructor/display_questions.html.twig', [
                'questions' => $questions,
                'proposals' => $resumeProposal,
            ]);
        }

        #[Route('instructor/creations/qcms', name: 'instructor_display_qcms', methods: ['GET'])]
        public function displayQcms(
            QcmRepository $qcmRepo,
            Security      $security
        ): Response
        {
            $qcms = $qcmRepo->findBy([
                'author' => $security->getUser(),
            ]);

            return $this->render('instructor/display_qcms.html.twig', [
                'qcms' => $qcms,
            ]);
        }

        #[Route('instructor/questions/modify_question/{question}', name: 'instructor_modify_question', methods: ['GET', 'POST'])]
        public function modifyQuestion(
            Request                $request,
            Question               $question,
            QuestionRepository     $questionRepository,
            ProposalRepository     $proposalRepository,
            EntityManagerInterface $manager
        ): Response
        {
            $releaseDateOnSession = $questionRepository->getSessionWithReleaseDate($question);
            if ($releaseDateOnSession)
            {
                $session = $releaseDateOnSession[0]['name'];
            }
            else
            {
                $session = null;
            }

            // GetQuestionById with release_date
            $releaseDate = $questionRepository->getQuestionWithReleaseDate($question);

            if ($releaseDate)
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
            foreach ($instanceQuestion->getProposals() as $beforeProp) {
                $arrayBeforeProp[] = $beforeProp->getId();
            }

            // création form
            $form = $this->createForm(CreateQuestionType::class, $instanceQuestion);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $count = 0;
                $persistPropCount = 0;
                $persistProp = [];
                foreach ($instanceQuestion->getProposals() as $prop) {
                    $bool = in_array($prop->getId(), $arrayBeforeProp);
                    // Si la prop est une déjà créer en db ou si son id est null alors si elle vient d'être créée.
                    if ($bool || $prop->getId() == null) {
                        // Si l'utilisateur a modifié la reponse
                        $prop->setQuestion($instanceQuestion);
                        $persistProp[] = $prop->getId();
                        $persistPropCount++;
                    }
                    // Si la reponse est une reponse correcte
                    if ($prop->getIsCorrectAnswer()) {
                        $count++;
                    }
                }

                // Set le champs ResponseType
                if ($count > 1)
                {
                    $instanceQuestion->setIsMultiple(true);
                }
                elseif ($count == 1)
                {
                    $instanceQuestion->setIsMultiple(false);
                }

                //Supprime le lien entre les proposals et la question que l'utilisateur ne veut plus
                $removeProp = array_diff($arrayBeforeProp, $persistProp);
                foreach ($removeProp as $id) {
                    $prop = $proposalRepository->find($id);
                    $manager->remove($prop);
                }

                $manager->persist($instanceQuestion);
                $manager->flush();

                $this->addFlash('success', 'La question a bien été modifiée.');
                return $this->redirectToRoute('instructor_display_questions');
            }

            return $this->render('instructor/modify_question.html.twig', [
                'form' => $form->createView(),
                'distribute' => $distribute,
                'session' => $session,
                "add" => false,
            ]);
        }

        #[Route('instructor/questions/create_question', name: 'instructor_create_question', methods: ['GET', 'POST'])]
        public function createQuestion(
            Request                $request,
            EntityManagerInterface $manager,
            InstructorRepository $instructorRepository
        ): Response
        {

//          TODO A enlever une fois que a connection avec google sera opérationnelle
            $user = $instructorRepository->find( $this->id );

            $questionEntity = new Question();

            $proposal1 = new Proposal();
            $proposal1->setWording('');

            $proposal2 = new Proposal();
            $proposal2->setWording('');

            $questionEntity->addProposal($proposal2);
            $questionEntity->addProposal($proposal1);


            // création form
            $form = $this->createForm(CreateQuestionType::class, $questionEntity);
            // accès aux données du form
            $form->handleRequest($request);


            // vérification des données soumises
            if ( $form->isSubmitted() && $form->isValid() )
            {
                $count = 0;
                $persitPropCount = 0;
                foreach( $questionEntity->getProposals() as $proposal )
                {
                    // set les proposals
                    $proposal->setQuestion($questionEntity);
                    $persitPropCount++;

                    // set le response type
                    if( $proposal->getIsCorrectAnswer() )
                    {
                        $count++;
                    }
                }
                if ($count > 1)
                {
                    $questionEntity->setIsMultiple("true");
                }
                elseif ($count == 1)
                {
                    $questionEntity->setIsMultiple("false");
                }

//              TODO A enlever une fois que a connection avec google sera opérationnelle
                $questionEntity->setAuthor($user);
//                $questionEntity->setAuthor($this->getUser());
                $questionEntity->setDifficulty(intval($form->get('difficulty')->getViewData()));

                //  validation et enregistrement des données du form dans la bdd
                $manager->persist($questionEntity);

                $manager->flush();

                //  redirect to route avec flash
                $this->addFlash(
                    'instructorAddQuestion',
                    'La question a été généré avec succès'
                );
                return $this->redirectToRoute('instructor_display_questions');

            }
            return $this->render('instructor/create_question.html.twig', [
                'form' => $form->createView(),
                "add" => true,
//                TODO A enlever une fois que a connection avec google sera opérationnelle
                'user' => $user
            ]);
            }

    #[Route('instructor/qcms/create_qcm_perso', name: 'instructor_create_qcm_perso', methods: ['GET', 'POST'])]
    public function createQcmPersonalized(
        Request              $request,
        InstructorRepository $instructorRepository,
        ModuleRepository     $moduleRepository,
        QuestionRepository   $questionRepository,
        Security             $security,
        QcmRepository        $qcmRepo,
        QcmInstanceRepository     $qcmInstanceRepository

    ): Response
    {

        /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
        // $userId=$instructorRepository->find($id);
        // $userId = $this->getUser()->getId();
        $userId =2;
        $linksInstructorSessionModule = $instructorRepository->find($userId)->getLinksInstructorSessionModule();

        $modules = [];
        foreach ($linksInstructorSessionModule as $linkInstructorSessionModule)
        {
            $modules[] = $linkInstructorSessionModule->getModule();
        }

        /**********************************************************************************/
        // Get module choiced
        $module = null;
        if ($request->get('module'))
        {
            $module = $moduleRepository->find($request->get('module'));
        }

        if ($module)
        {
            $qcmGenerator = new QcmGeneratorHelper($questionRepository, $security);
            $generatedQcm = $qcmGenerator->generateRandomQcm($module);
            $customQuestions = $questionRepository->findBy(['isOfficial' => false, 'isMandatory' => false, 'module' => $module->getId(), 'author' => $userId]);
            $officialQuestions = $questionRepository->findBy(['isOfficial' => true, 'isMandatory' => false, 'module' => $module->getId()]);
             //qcm instance
            // $qcms = $qcmRepo->findBy(["module"=>$module->getId()]);
            // $qcmInstances = $qcmInstanceRepository->findBy(["qcm"=>$qcms]);
            $qcms = $module->getQcms();
            $moduleQuestions = $module->getQuestions();


        }


        // $qcmInstanceFromOfficialQcm=[];
        // foreach($qcmInstances as $qcm){
        //     $qcmInstanceFromOfficialQcm[]=["id"=>$qcm->getQcm()->getId(),"questions"=>$qcm->getQcm()->getQuestionsCache()];

        // }
        //pour chaque q recup les qcm lié et enregistré le nombr d'instance de c'est q en tant que val


        $qcmInstancesByQuestion = [];
        foreach($moduleQuestions as $moduleQuestion){

            $count=0;
            foreach($moduleQuestion->getQcms() as $moduleQuestionQcm ) {
                $count+=count($moduleQuestionQcm->getQcmInstances());
              }
             $qcmInstancesByQuestion[$moduleQuestion->getId()]=$count;
            }







        /********************************************************************************/
        return $this->render('instructor/create_qcm_perso.html.twig', [
            'modules' => $modules,
            'customQuestions' => $module ? $customQuestions : null,
            'officialQuestions' => $module ? $officialQuestions : null,
            'generatedQcm' => $module ? $generatedQcm : null,
            // temporaire voir todo pour connection
            'user'=>$userId,
            // 'qcmInstanceFromOfficialQcm'=>$qcmInstanceFromOfficialQcm,
            'qcms'=>$qcms,
            'qcmInstancesByQuestion'=>$qcmInstancesByQuestion

        ]);
    }

        #[Route('instructor/questions/upDate_fetch', name: 'instructor_questions_update_fetch', methods: ['POST'])]
        public function upDateQuestionFetch(
            ValidatorInterface     $validator,
            Request                $request,
            InstructorRepository   $instructorRepository,
            ModuleRepository       $moduleRepository,
            QuestionRepository     $questionRepository,
            EntityManagerInterface $entityManager
        ): Response
        {
            $data = (array) json_decode($request->getContent());
            $question = new Question();
            $module = $moduleRepository->find($data['module']);
            $question->setModule($module);
            $question->setWording($data['wording']);
            $question->setIsMultiple($data['isMultiple']);
            $question->setDifficulty(1);
            $question->setExplanation('null');
            $author = $instructorRepository->find($this->getUser()->getId());
            $question->setAuthor($author);
            $question->setIsMandatory(0);
            $question->setIsOfficial(0);
            $question->setIsEnabled(1);

            foreach ($data['proposals'] as $proposal)
            {
                $newProposal = new Proposal();
                $newProposal->setWording($proposal->wording);
                $newProposal->setIsCorrectAnswer($proposal->isCorrectAnswer);
                $validator->validate($newProposal);
                $question->addProposal($newProposal);
            };

            $validator->validate($question);

            $entityManager->persist($question);
            $entityManager->flush();

            $questionResponse = $questionRepository->find($question->getId());

            /*TODO Débuger le jsonResponse*/
            return new JsonResponse($questionResponse);
        }

    // methode Post non permise car route non trouvée donc method Get Ok
    #[Route('instructor/qcms/create_fetch', name: 'instructor_qcm_create_fetch', methods: ['POST'])]
    public function createQcmFetch(
        ValidatorInterface     $validator,
        Request                $request,
        InstructorRepository   $instructorRepository,
        QuestionRepository     $questionRepository,
        ModuleRepository       $moduleRepository,
        EntityManagerInterface $entityManager,
        QcmGeneratorHelper $generatorHelper

    ): Response
    {


        $data = (array)json_decode($request->getContent());
        $qcm = new Qcm();
          /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
          $author=$instructorRepository->find(2);
        // $author = $instructorRepository->find($this->getUser()->getId());
        $qcm->setAuthor($author);

        $qcm->setTitle($data['name']);
        if ($data['level'] === 'Difficile')
        {
            $level = 1;
        }
        elseif ($data['level'] === 'Moyen')
        {
            $level = 2;
        }
        else
        {
            $level = 3;
        }
        $qcm->setDifficulty($level);
        $qcm->setIsEnabled(1);
        $qcm->setIsOfficial(0);
        $qcm->setIsPublic($data['isPublic']);
        $module = $moduleRepository->find($data['module']);
        $qcm->setModule($module);

            /*TODO voir avec Mathieu pour utiliser le hepler pour cette partie*/
        $questionsCache = $generatorHelper->generateQuestionCache($data['questions']);
//            $questionsCache = [];
//            foreach ($data['questions'] as $question)
//            {
//                $question = $questionRepository->find($question->id);
//                $questionProposals = $question->getProposals();
//                $proposalsCache = [];
//                foreach ($questionProposals as $questionProposal)
//                {
//                    $proposalsCache[] = [
//                        'id' => $questionProposal->getId(),
//                        'wording' => $questionProposal->getWording(),
//                        'isCorrectAnswer' => $questionProposal->getIsCorrectAnswer(),
//                    ];
//                }
//                $questionsCache[] = [
//                    'id' => $question->getId(),
//                    'wording' => $question->getWording(),
//                    'isMultiple' => $question->getIsMultiple(),
//                    'difficulty' => $question->getDifficulty(),
//                    'proposals' => $proposalsCache
//                ];
//            }

            $qcm->setQuestionsCache($questionsCache);

            $validator->validate($qcm);
            $entityManager->persist($qcm);
            $entityManager->flush();

        /*redirection voir js*/
        $this->addFlash('success', 'Le qcm a bien été modifiée.');
        // // return $this->redirectToRoute('instructor_display_questions');
        return $this->json("ok",200);

    }


        #[Route('instructor/qcms/create_official_qcm', name: 'instructor_create_qcm', methods: ['GET', 'POST'])]
        public function createOfficialQcm(
            Security               $security,
            SessionRepository      $sessionRepository,
            InstructorRepository   $instructorRepository,
            Request                $request,
            QuestionRepository     $questionRepository,
            ModuleRepository       $moduleRepository,
            EntityManagerInterface $manager
        ): Response
        {
            $dayOfWeekEnd = array("Saturday", "Sunday");
//            $userId = $security->getUser();
            /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
            $userId = 1;
            $sessionAndModuleByInstructor = $instructorRepository->find($userId)->getLinksInstructorSessionModule();

            foreach ($sessionAndModuleByInstructor as $sessionAndModule)
            {
                $sessionId = $sessionAndModule->getSession()->getId();
                $moduleId = $sessionAndModule->getModule()->getId();
                $sessions = $sessionRepository->findBy(['id' => $sessionId]);
                $modules = $moduleRepository->findBy(['id' => $moduleId]);
            }

            $formData = $request->query->all();

            if (count($formData) != 0)
            {
                $module = $moduleRepository->find($formData["module"]);
                $qcmGenerator = new QcmGeneratorHelper($questionRepository, $security);
                /*TODO A enlever une fois que a connection avec google sera opérationnelle ( $instructorRepository )*/
                $qcm = $qcmGenerator->generateRandomQcm($module,$instructorRepository, false);
                $manager->persist($qcm);

                $linksSessionStudent = $sessionRepository->find($formData["session"])->getLinksSessionStudent();
                $students = [];

                foreach ($linksSessionStudent as $linkSessionStudent)
                {
                    $students[] = $linkSessionStudent->getStudent();
                }

                foreach ($students as $student)
                {
                    $qcmInstance = new QcmInstance();
                    $qcmInstance->setStudent($student);
                    $qcmInstance->setQcm($qcm);
                    $qcmInstance->setCreatedAtValue();
                    $qcmInstance->setUpdateAtValue();

                    //START TIME AND END TIME
                    $dayOfCreationOfQcmInstance = $qcmInstance->getCreatedAt();
                    if ($dayOfCreationOfQcmInstance)
                    {
                        $dateOfCreationFormat = date_format($dayOfCreationOfQcmInstance, "Y-m-d H:i:s");
                        $newDateTimeForStartTime = date("Y-m-d 13:00:00", strtotime($dateOfCreationFormat . '+ 5 days'));
                        $startTime = new \DateTime($newDateTimeForStartTime);
                        $qcmInstance->setStartTime($startTime);
                        $newDateTimeForEndTime = date("Y-m-d H:i:s", strtotime($newDateTimeForStartTime . '+ 4hours'));
                        $endTime = new \Datetime($newDateTimeForEndTime);

                        //  DAY ADDITION IF ENDTIME = A DAY OF WEEK  voir array -> dayOfweek
                        $endTimeTextualFormat = date_format($endTime, 'l');
                        if ($endTime && $endTimeTextualFormat === $dayOfWeekEnd[0])
                        {
                            $endTime = date_format($endTime, "Y-m-d H:i:s");
                            $endTimeFormatNum = date("Y-m-d H:i:s", strtotime($endTime . '+ 2 days'));
                            $endTime = new \DateTime($endTimeFormatNum);
                        }
                        elseif ($endTime && $endTimeTextualFormat === $dayOfWeekEnd[1])
                        {
                            //autre methode si format de celle ci gardé sinon la convertir en celle d'en haut
                            $endTime = $endTime->add(new DateInterval("P1D"));
                        }
                        $qcmInstance->setEndTime($endTime);
                        //mettre dans un tableau saturday sunday
                        //et a partir de la endtdate  la convertir a un format mot et faire une condition
                        // si la date contient un des jours du tableau (ou une condition swich pour voir si c'est égale a un des element ddans ) ajouter +1 ou +2 enfonction du jour pour que ça tombe un lundi

                        //on recupère on crée une variable dans lequel on met le string de getcreatedat ,
                        //puis on place la varible dans datetime puis on lei donne un format et ainsi de suite
                    }

                    $manager->persist($qcmInstance);
                    $manager->flush();

                    //  redirect to route avec flash
                    $this->addFlash(
                        'success',
                        'Le qcm a été généré avec succès'
                    );
                    return $this->redirectToRoute('welcome_instructor');
                }

            }

            return $this->render('instructor/generate_official_qcm.html.twig', [
                'sessions' => $sessions,
                'modules' => $modules,
            ]);
        }

        #[Route('instructor/plan_qcm', name: 'instructor_plan_qcm', methods: ['GET', 'POST'])]
        public function planQcm(SessionRepository $sessionRepo): Response
        {
            /*TODO A enlever une fois que a connection avec google sera opérationnelle*/
            $instructorSessions = $sessionRepo->getInstructorSessions($this->id);

//            $instructorSessions = $sessionRepo->getInstructorSessions($this->getUser());

            return $this->render('instructor/plan_qcm.html.twig', [
                'instructorSessions' => $instructorSessions,
            ]);
        }

        #[Route('instructor/qcm-planner/getSessionModules/{session}', name: 'instructor_get_session_modules_ajax', methods: ['GET'])]
        public function ajaxGetSessionModules(
            LinkInstructorSessionModuleRepository $LinkSessionModuleRepo,
            Session $session = null
        ): JsonResponse
        {
            if ($session)
            {
                $linksSessionModules = $LinkSessionModuleRepo->findBy(['session' => $session]);
                $modules = array_map(function ($linkSessionModule) {
                    return $linkSessionModule->getModule();
                }, $linksSessionModules);

                return $this->json($modules, 200, [], ['groups' => 'module:read']);
            }
            return new JsonResponse();
        }

        #[Route('instructor/qcm-planner/getSessionStudents/{session}', name: 'instructor_get_session_students_ajax', methods: ['GET'])]
        public function ajaxGetSessionStudents(
            LinkSessionStudentRepository $LinkSessionStudentRepo,
            Session $session = null
        ): JsonResponse
        {
            if ($session)
            {
                $LinksSessionStudent = $LinkSessionStudentRepo->findBy(['session' => $session]);
                $students = array_map(function ($LinkSessionStudent) {
                    return $LinkSessionStudent->getStudent();
                }, $LinksSessionStudent);

                return $this->json($students, 200, [], ['groups' => 'user:read']);
            }
            return new JsonResponse();
        }

        #[Route('instructor/qcm-planner/getModuleQcms/{module}', name: 'instructor_get_module_qcms_ajax', methods: ['GET'])]
        public function ajaxGetModuleQcms(
            Module $module = null
        ): JsonResponse
        {
            if ($module)
            {
                $qcms = $module->getQcms();
                return $this->json($qcms, 200, [], ['groups' => 'qcm:read']);
            }
            return new JsonResponse();
        }

        #[Route('instructor/qcm-planner/distribute', name: 'instructor_distribue_qcm')]
        public function planQcmToStudents(
            Request                $request,
            QcmRepository          $qcmRepo,
            StudentRepository      $studentRepo,
            EntityManagerInterface $manager
        ): Response
        {
            $qcm = $qcmRepo->find(intval($request->get('qcm')));
            $startTime = new \DateTime($request->get('start-time'));
            $endTime = new \DateTime($request->get('end-time'));
            $students = array_map(function ($studentId) use ($studentRepo) {
                return $studentRepo->find(intval($studentId));
            }, $request->get('students'));
            foreach ($students as $student)
            {
                $qcmInstance = new QcmInstance();
                $qcmInstance->setStudent($student);
                $qcmInstance->setQcm($qcm);
                $qcmInstance->setStartTime($startTime);
                $qcmInstance->setEndTime($endTime);
                $qcmInstance->setCreatedAtValue();
                $qcmInstance->setUpdateAtValue();
                $manager->persist($qcmInstance);
            }
            $manager->flush();

            $this->addFlash('success', 'La programmation du qcm a bien été enregistrée.');
            return $this->redirectToRoute('welcome_instructor');
        }

        #[Route('instructor/qcms/distributed_qcms',name:'instructor_distributed_qcms',methods:['GET','POST'])]
        public function distributedQcmToStudent(
            InstructorRepository        $instructorRepository,
            SessionRepository           $sessionRepository,
            ModuleRepository            $moduleRepository,
            QcmRepository               $qcmRepository,
        ):Response
        {
            $userId = $this->id;
            $sessionAndModuleByInstructor = $instructorRepository->find($userId)->getLinksInstructorSessionModule();

            foreach ($sessionAndModuleByInstructor as $sessionAndModuleByInstructor)
            {
                $sessions = $sessionRepository->getInstructorSessions();
                $modules = $moduleRepository->getModuleSessions($sessions[0]->getId());
                $qcm = $qcmRepository->getQcmModules(1);
            }

            return $this->render('instructor/distributed_qcms.html.twig', [
                        'sessions' => $sessions,
                        'modules' => $modules,
                        'qcm' => $qcm,
                ]);
        }

        #[Route('instructor/qcms/distributed_qcms/{session}',name:'instructor_distributed_qcms_get_module_ajax',methods:['GET'])]
        public function ajaxGetSessionByInstructor(
            Session $session,
            ModuleRepository $moduleRepository,
        ):JsonResponse
        {

            $modules = $moduleRepository->getModuleSessions($session->getId());
            $modulesName = [];
            foreach ( $modules as $module ){
                $modulesName[] =  ['name' => $module->getTitle(), 'id' => $module->getId()];
            }
            return $this->json($modulesName);
        }

        #[Route('instructor/qcms/distributed_students/{qcm}',name:'instructor_distributed_qcms_get_student_ajax',methods:['GET'])]
        public function ajaxGetStudentByQcm(
            Qcm $qcm = null
        ): JsonResponse
        {
            if ($qcm)
            {
                $qcmInstances = $qcm->getQcmInstances()->toArray();
                dump($qcmInstances);
                $students = array_map( function($qcmInstance){
                    dump($qcmInstance->getStudent());
                    return [
                        'student' => $qcmInstance->getStudent(),
                        'result' => $qcmInstance->getResult()
                    ];
                }, $qcmInstances);
                $studentResponse = [];
                foreach ($students as $student){
                    if(!in_array($student, $studentResponse)){
                        $studentResponse[] = $student;
                    }
                }
                dump($studentResponse);

                return $this->json($studentResponse, 200, [], ['groups' => 'user:read']);
            }
            return new JsonResponse();
        }

        #[Route('instructor/dashboard',name:'instructor_dashboard',methods:['GET'])]
        public function dashboard(
            InstructorRepository        $instructorRepository,
            SessionRepository           $sessionRepository,
            ModuleRepository            $moduleRepository,
        ):Response
        {
            /*TODO A enlever et mettre à jour quand connexion google sera rétablie */
            $userId = $this->id;
            $sessions = $sessionRepository->getInstructorSessions($userId);
            $modules = $moduleRepository->getModuleSessions($sessions[0]->getId());

            return $this->render('instructor/dashboard.html.twig', [
                'sessions' => $sessions,
                'modules' => $modules,
            ]);
        }

        #[Route('instructor/dashboard/{session}',name:'instructor_get_module_student_ajax',methods:['GET'])]
        public function ajaxGetSessionAndStudentByInstructor(
            Session $session,
            ModuleRepository $moduleRepository,
        ):JsonResponse
        {
            $modules = $moduleRepository->getModuleSessions($session->getId());
            $modulesName = [];
            foreach ( $modules as $module ){
                $modulesName[] =  ['name' => $module->getTitle(), 'id' => $module->getId()];
            }
            return $this->json($modulesName);
        }

        #[Route('instructor/dashboard/{session}/{module}',name:'instructor_get_student_ajax',methods:['GET'])]
        #[Entity('Session', options: ['id' => 'session'])]
        public function ajaxGetStudentByInstructorForDashBoard(
            Session $session,
            Module $module,
            ResultRepository $resultRepository
        ):JsonResponse
        {
            $maxScoreStudents = $resultRepository->maxScoreByModuleAndSession($session->getId(), $module->getId());
            return $this->json($maxScoreStudents);
        }

        #[Route('instructor/dashboard/{session}/{moduleId}/{studentId}',name:'instructor_get_qcms_by_student_ajax',methods:['GET'])]
        public function ajaxGetQcmsByStudentForDashBoard(
            $studentId,
            Session $session,
            $moduleId,
            QcmRepository $qcmRepository,
        ):JsonResponse
        {
            $qcms = $qcmRepository->getQcmsByStudentAndModule(intval($moduleId),intval($studentId));
            return $this->json($qcms);

        }

        #[Route('instructor/qcm_student/correction/{result}/{comment}',name:'instructor_qcm_student_add_comment_ajax',methods:['GET'])]
        public function ajaxAddCommentQcmStudent(
            Result $result,
            $comment,
            EntityManagerInterface $manager
        ):JsonResponse
        {
//            dd($comment);
            $result->setInstructorComment($comment);
            $manager->flush();
            return $this->json('Le commentaire a bien été ajouté');

        }






    }