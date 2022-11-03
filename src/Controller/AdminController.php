<?php

namespace App\Controller;

use App\Entity\Main\Admin;
use App\Entity\Main\Instructor;
use App\Entity\Main\LinkInstructorSessionModule;
use App\Entity\Main\LinkSessionModule;
use App\Entity\Main\Session;
use App\Entity\Main\Student;
use App\Entity\Main\User;
use App\Form\RegistrationFormType;
use App\Repository\BugReportRepository;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResultRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $doctrine;

    public function __construct( ManagerRegistry $doctrine )
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/stats', name: 'app_admin_stats')]
    public function stats(): Response
    {
        return $this->render('admin/stats.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('admin/manage-qcms', name: 'admin_manage_qcms')]
    public function manageQcms( QcmRepository $qcmRepo ): Response
    {
        return $this->render('admin/manage_qcms.html.twig', [
            'qcms' => $qcmRepo->findAll()
        ]);
    }

    #[Route('admin/manage-questions', name: 'admin_manage_questions')]
    public function manageQuestions( QuestionRepository $questionRepo ): Response
    {
        return $this->render('admin/manage_questions.html.twig', [
            'questions' => $questionRepo->findAll()
        ]);
    }

    #[Route('admin/manage-users', name: 'admin_manage_users')]
    public function manageUsers( UserRepository $userRepo ): Response
    {
        return $this->render('admin/manage_users.html.twig', [
            'users' => $userRepo->findAll()
        ]);
    }

    #[Route('admin/update-user/{user}/{role}', name: 'admin_update_users_ajax', methods: ['GET'])]
    public function ajaxFetchUsers(
        User $user,
        EntityManagerInterface $manager,
        $role
    ): JsonResponse
    {
        $user->setRoles([$role]);

        $manager->persist($user);
        $manager->flush();

        return $this->json( $user, 200, [],['groups' => 'user:read'] );
    }

    #[Route('admin/user-details/{user}', name: 'admin_user_details_ajax')]
    public function ajaxUserDetails( User $user ): JsonResponse
    {
        $conn = $this->doctrine->getConnection('dbsuivi');
        if( in_array('ROLE_INSTRUCTOR', $user->getRoles()) )
        {
            $sql = "SELECT sessions.name as sessionName
                FROM daily
                LEFT JOIN sessions ON daily.id_session = sessions.id
                LEFT JOIN users ON daily.id_user = users.id
                WHERE users.email = :useremail AND daily.date >= NOW()
                ";
            $params = [
                'useremail' => 'matthieu.fergola@gmail.com'
            ];
            $currentSession = $conn
                ->prepare($sql)
                ->executeQuery($params)
                ->fetchAll();
        }
        elseif ( in_array('ROLE_STUDENT', $user->getRoles()) )
        {
            $sql = "SELECT sessions.name as sessionName
                FROM daily
                LEFT JOIN sessions ON daily.id_session = sessions.id
                LEFT JOIN link_students_daily ON link_students_daily.id_daily = daily.id  
                WHERE users.email = :useremail AND daily.date >= NOW()
                ";
            $params = [
                'useremail' => 'dorine.journet@3wa.io'
            ];
            $currentSession = $conn
                ->prepare($sql)
                ->executeQuery($params)
                ->fetchAll();
        }

        return $this->json( [$user, $currentSession], 200, [],['groups' => 'user:read'] );
    }

    #[Route('admin/manage-sessions', name: 'admin_manage_sessions')]
    public function manageSessions( SessionRepository $sessionRepo ): Response
    {
        return $this->render('admin/manage_sessions.html.twig', [
            'sessions' => $sessionRepo->findAll()
        ]);
    }

    #[Route('admin/session-students/{session}', name: 'admin_session_users_ajax')]
    public function ajaxSessionStudents( Session $session ): JsonResponse
    {
        $linksSessionStudent = $session->getLinksSessionStudent()->toArray();
        $students = array_map( function($linkSessionStudent){
            return $linkSessionStudent->getStudent();
        }, $linksSessionStudent);

        return $this->json( $students, 200, [],['groups' => 'user:read'] );
    }

    #[Route('admin/session-modules/{session}', name: 'admin_session_modules_ajax')]
    public function ajaxSessionModules( Session $session ): JsonResponse
    {
        $linksSessionModule = $session->getLinksSessionModule();

        $modules = [];

        foreach( $linksSessionModule as $linkSessionModule )
        {
            $modules[] = [
                'id' => $linkSessionModule->getModule()->getId(),
                'title' => $linkSessionModule->getModule()->getTitle(),
                'startDate' => $linkSessionModule->getStartDate(),
                'endDate' => $linkSessionModule->getEndDate()
            ];
        }

        return $this->json( $modules, 200, [],['groups' => 'user:read'] );
    }

    #[Route('admin/manage-modules', name: 'admin_manage_modules')]
    public function manageModules( ModuleRepository $moduleRepo ): Response
    {
        return $this->render('admin/manage_modules.html.twig', [
            'modules' => $moduleRepo->findAll()
        ]);
    }

    // TODO: Probablement à supprimer / relier à la DB de suivi (voir avec Pascal)
    #[Route('/admin/new-user', name: 'app_new_user')]
    public function newUser(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setCreatedAt(new \DateTime());

            $entityManager->persist($user);

            if( in_array( 'student', $user->getRoles() ) || in_array( 'instructor', $user->getRoles() ) || in_array( 'admin', $user->getRoles() )  )
            {
                $student = new Student();
                $student->getMoodleId( $user->getMoodleId() );
                $student->setFirstName( $user->getFirstName() );
                $student->setLastName( $user->getLastName() );
                $student->setBirthDate( $user->getBirthDate() );
                $student->setEmail( $user->getFirstName() . '.' . $user->getLastName() . '@3wa.io' );
                $student->setCreatedAtValue();

                $entityManager->persist($student);

                if( in_array( 'instructor', $user->getRoles() ) || in_array( 'admin', $user->getRoles() ) ) {
                    $instructor = new Instructor();
                    $instructor->setFirstName( $user->getFirstName() );
                    $instructor->setLastName( $user->getLastName() );
                    $instructor->setBirthDate( $user->getBirthDate() );
                    $instructor->setPhone( '0600000000' );
                    $instructor->setEmail( $user->getEmail() );
                    $instructor->setCreatedAtValue();

                    $entityManager->persist($instructor);

                    if (in_array('admin', $user->getRoles())) {
                        $admin = new Admin();
                        $admin->setFirstName($user->getFirstName());
                        $admin->setLastName($user->getLastName());
                        $admin->setCreatedAtValue();

                        $entityManager->persist($admin);
                    }
                }
            }

            $entityManager->flush();

            // envoi d'email ici (ex: avec id et password pour le nouvel inscrit)

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function getDataFromSuivi( $sql, $params = [] )
    {
        $conn = $this->doctrine->getConnection('dbsuivi');
        $stmt = $conn->prepare($sql);
        if( $params !== [] )
        {
            foreach( $params as $key => $value )
            {
                $stmt->bindValue($key, $value);
            }
        }
        return $stmt->executeQuery()->fetchAllAssociative();

    }

    #[Route('admin/bug-reports', name: 'admin_bug_reports')]
    public function bugReports( BugReportRepository $bugReportRepo ): Response
    {
        return $this->render('admin/bug_reports.html.twig', [
            'reportedBugs' => $bugReportRepo->findAll()
        ]);
    }

    // STATS -----------------------------------------------------------------------------------------------------------

    // Modules stats
    #[Route('admin/stats/modules' ,name: 'admin_stats_modules')]
    public function statsModules(ModuleRepository $moduleRepo) : Response
    {
        return $this->render('admin/stats/modules.html.twig', [
            'modules' => $moduleRepo->findAll(),
        ]);
    }

    #[Route('admin/stats/fetch/modules-success-rate' ,name: 'admin_fetch_modules_success_rate', methods: ['GET'])]
    public function fetchModulesSuccessRate(ModuleRepository $moduleRepo) : JsonResponse
    {
        $ratesByModule = [];

        $modules = $moduleRepo->findAll();
        foreach( $modules as $module )
        {
            $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                return $qcm->getIsOfficial();
            });

            $scores = [];
            foreach( $moduleOfficialQcms as $moduleOfficialQcm)
            {
                $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                foreach( $qcmInstances as $qcmInstance )
                {
                    $result = $qcmInstance->getResult();
                    if( $result )
                    {
                        $scores[] = $result->getScore();
                    }
                }
            }
            $scoresOverFifty = array_filter($scores, function($score){ return $score >= 50; });
            $ratesByModule[] = [
                'title' => $module->getTitle(),
                'averageScore' => count($scores) > 0 ? array_sum($scores) / count($scores) : 0,
                'successRate' => count($scores) > 0 ? count($scoresOverFifty) / count($scores) : 0,
            ];
        }

        return $this->json( $ratesByModule );
    }

    #[Route('admin/stats/fetch/stacks-success-rate' ,name: 'admin_fetch_stacks_success_rate', methods: ['GET'])]
    public function fetchStacksSuccessRate(ModuleRepository $moduleRepo) : JsonResponse
    {
        $ratesByStack = [];
        $modules = $moduleRepo->findAll();

        $stacksNotUniq = array_map( function($module){
            return preg_replace('/[0-9]+/', '' ,$module->getTitle() );
        }, $modules );

        $stacks = array_unique( $stacksNotUniq );

        foreach( $stacks as $stack )
        {
            $stackModules = $moduleRepo->findAllModulesByBaseName($stack);

            $stackScores = [];
            $stackOverFiftyScores = [];
            foreach( $stackModules as $module )
            {
                $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                    return $qcm->getIsOfficial();
                });

                $scores = [];
                foreach( $moduleOfficialQcms as $moduleOfficialQcm)
                {
                    $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                    foreach( $qcmInstances as $qcmInstance )
                    {
                        $result = $qcmInstance->getResult();
                        if( $result )
                        {
                            $scores[] = $result->getScore();
                        }
                    }
                }
                $stackScores = array_merge( $stackScores, $scores );
                $stackOverFiftyScores = array_merge( $stackOverFiftyScores, array_filter($scores, function($score){ return $score >= 50; }) );
            }
            $ratesByStack[] = [
                'title' => $stack,
                'averageScore' => count($stackScores) > 0 ? array_sum($stackScores) / count($stackScores) : 0,
                'successRate' => count($stackScores) > 0 ? count($stackOverFiftyScores) / count($stackScores) : 0,
            ];
        }

        return $this->json( $ratesByStack );
    }

    // Session stats
    #[Route('admin/stats/session/{session}' ,name: 'admin_stats_session')]
    public function statsSession(Session $session) : Response
    {
        return $this->render('admin/stats/session.html.twig', [
            'session' => $session,
        ]);
    }

    #[Route('admin/stats/fetch/session-modules-success-rate/{session}' ,name: 'admin_fetch_session_modules_success_rate', methods: ['GET'])]
    public function fetchSessionModulesSuccessRate(
        Session $session,
        LinkSessionModuleRepository $linkSessionModuleRepo
    ) : JsonResponse
    {
        $ratesByModule = [];

        $linksSessionModule = $linkSessionModuleRepo->findBy( ['session' => $session] );

        $modules = [];

        foreach( $linksSessionModule as $linkSessionModule )
        {
            $modules[] = $linkSessionModule->getModule();
        }

        foreach( $modules as $module )
        {
            $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                return $qcm->getIsOfficial();
            });

            $scores = [];
            foreach( $moduleOfficialQcms as $moduleOfficialQcm)
            {
                $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                foreach( $qcmInstances as $qcmInstance )
                {
                    $result = $qcmInstance->getResult();
                    if( $result )
                    {
                        $scores[] = $result->getScore();
                    }
                }
            }
            $scoresOverFifty = array_filter($scores, function($score){ return $score >= 50; });
            $ratesByModule[] = [
                'title' => $module->getTitle(),
                'averageScore' => count($scores) > 0 ? array_sum($scores) / count($scores) : 0,
                'successRate' => count($scores) > 0 ? count($scoresOverFifty) / count($scores) : 0,
            ];
        }

        return $this->json( $ratesByModule );
    }

    #[Route('admin/stats/fetch/session-stacks-success-rate/{session}' ,name: 'admin_fetch_session_stacks_success_rate', methods: ['GET'])]
    public function fetchSessionStacksSuccessRate(
        Session $session,
        LinkSessionModuleRepository $linkSessionModuleRepo,
        ModuleRepository $moduleRepo
    ) : JsonResponse
    {
        $ratesByStack = [];

        $linksSessionModule = $linkSessionModuleRepo->findBy( ['session' => $session] );

        $modules = [];

        foreach( $linksSessionModule as $linkSessionModule )
        {
            $modules[] = $linkSessionModule->getModule();
        }

        $stacksNotUniq = array_map( function($module){
            return preg_replace('/[0-9]+/', '' ,$module->getTitle() );
        }, $modules );

        $stacks = array_unique( $stacksNotUniq );

        foreach( $stacks as $stack )
        {
            $stackModules = $moduleRepo->findAllModulesByBaseName($stack);

            $stackScores = [];
            $stackOverFiftyScores = [];
            foreach( $stackModules as $module )
            {
                $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                    return $qcm->getIsOfficial();
                });

                $scores = [];
                foreach( $moduleOfficialQcms as $moduleOfficialQcm)
                {
                    $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                    foreach( $qcmInstances as $qcmInstance )
                    {
                        $result = $qcmInstance->getResult();
                        if( $result )
                        {
                            $scores[] = $result->getScore();
                        }
                    }
                }
                $stackScores = array_merge( $stackScores, $scores );
                $stackOverFiftyScores = array_merge( $stackOverFiftyScores, array_filter($scores, function($score){ return $score >= 50; }) );
            }
            $ratesByStack[] = [
                'title' => $stack,
                'averageScore' => count($stackScores) > 0 ? array_sum($stackScores) / count($stackScores) : 0,
                'successRate' => count($stackScores) > 0 ? count($stackOverFiftyScores) / count($stackScores) : 0,
            ];
        }

        return $this->json( $ratesByStack );
    }

    // Student stats
    #[Route('admin/stats/student/{student}' ,name: 'admin_stats_student')]
    public function statsStudent(Student $student) : Response
    {
        return $this->render('admin/stats/student.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('admin/stats/fetch/student-modules-success-rate/{student}' ,name: 'admin_fetch_session_modules_success_rate', methods: ['GET'])]
    public function fetchStudentModulesSuccessRate(
        Student $student,
        LinkSessionStudentRepository $linkSessionStudentRepo
    ) : JsonResponse
    {
        $ratesByModule = [];

        $linksSessionStudent = $linkSessionStudentRepo->findBy( ['student' => $student] );

        $sessions = [];

        foreach( $linksSessionStudent as $linkSessionStudent )
        {
            $sessions[] = $linkSessionStudent->getSession();
        }

        $linksSessionModule = [];

        foreach( $sessions as $session )
        {
            $linksSessionModule = array_merge( $linksSessionModule, $session->getLinksSessionModule()->toArray() );
        }

        $modules = [];

        foreach( $linksSessionModule as $linkSessionModule )
        {
            $modules[] = $linkSessionModule->getModule();
        }

        foreach( $modules as $module )
        {
            $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                return $qcm->getIsOfficial();
            });

            $scores = [];
            foreach( $moduleOfficialQcms as $moduleOfficialQcm)
            {
                $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                foreach( $qcmInstances as $qcmInstance )
                {
                    $result = $qcmInstance->getResult();
                    if( $result )
                    {
                        $scores[] = $result->getScore();
                    }
                }
            }
            $scoresOverFifty = array_filter($scores, function($score){ return $score >= 50; });
            $ratesByModule[] = [
                'title' => $module->getTitle(),
                'averageScore' => count($scores) > 0 ? array_sum($scores) / count($scores) : 0,
                'successRate' => count($scores) > 0 ? count($scoresOverFifty) / count($scores) : 0,
            ];
        }

        return $this->json( $ratesByModule );
    }

    #[Route('admin/stats/fetch/student-stacks-success-rate/{student}' ,name: 'admin_fetch_student_stacks_success_rate', methods: ['GET'])]
    public function fetchStudentStacksSuccessRate(
        Student $student,
        LinkSessionStudentRepository $linkSessionStudentRepo,
        ModuleRepository $moduleRepo
    ) : JsonResponse
    {
        $ratesByStack = [];

        $linksSessionStudent = $linkSessionStudentRepo->findBy( ['student' => $student] );

        $sessions = [];

        foreach( $linksSessionStudent as $linkSessionStudent )
        {
            $sessions[] = $linkSessionStudent->getSession();
        }

        $linksSessionModule = [];

        foreach( $sessions as $session )
        {
            $linksSessionModule = array_merge( $linksSessionModule, $session->getLinksSessionModule()->toArray() );
        }

        $modules = [];

        foreach( $linksSessionModule as $linkSessionModule )
        {
            $modules[] = $linkSessionModule->getModule();
        }

        $stacksNotUniq = array_map( function($module){
            return preg_replace('/[0-9]+/', '' ,$module->getTitle() );
        }, $modules );

        $stacks = array_unique( $stacksNotUniq );

        foreach( $stacks as $stack )
        {
            $stackModules = $moduleRepo->findAllModulesByBaseName($stack);

            $stackScores = [];
            $stackOverFiftyScores = [];
            foreach( $stackModules as $module )
            {
                $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                    return $qcm->getIsOfficial();
                });

                $scores = [];
                foreach( $moduleOfficialQcms as $moduleOfficialQcm)
                {
                    $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                    foreach( $qcmInstances as $qcmInstance )
                    {
                        $result = $qcmInstance->getResult();
                        if( $result )
                        {
                            $scores[] = $result->getScore();
                        }
                    }
                }
                $stackScores = array_merge( $stackScores, $scores );
                $stackOverFiftyScores = array_merge( $stackOverFiftyScores, array_filter($scores, function($score){ return $score >= 50; }) );
            }
            $ratesByStack[] = [
                'title' => $stack,
                'averageScore' => count($stackScores) > 0 ? array_sum($stackScores) / count($stackScores) : 0,
                'successRate' => count($stackScores) > 0 ? count($stackOverFiftyScores) / count($stackScores) : 0,
            ];
        }

        return $this->json( $ratesByStack );
    }

    // Instructor stats
    #[Route('admin/stats/instructor/{instructor}' ,name: 'admin_stats_instructor')]
    public function statsInstructor(Instructor $instructor) : Response
    {
        return $this->render('admin/stats/instructor.html.twig', [
            'instructor' => $instructor,
        ]);
    }

    #[Route('admin/stats/fetch/instructor-modules-success-rate/{instructor}' ,name: 'admin_fetch_instructor_modules_success_rate', methods: ['GET'])]
    public function fetchInstructorModulesSuccessRate(
        Instructor $instructor,
        LinkInstructorSessionModuleRepository $linkInstructorSessionModuleRepo
    ) : JsonResponse
    {
        $ratesByModule = [];

        $linksInstructorSessionModule = $linkInstructorSessionModuleRepo->findBy( ['instructor' => $instructor] );

        $modules = [];
        $students = [];
        foreach( $linksInstructorSessionModule as $linkInstructorSessionModule )
        {
            $modules[] = $linkInstructorSessionModule->getModule();
            $linksSessionStudent = $linkInstructorSessionModule->getSession()->getLinksSessionStudent();
            foreach ( $linksSessionStudent as $linkSessionStudent )
            {
                $students[] = $linkSessionStudent->getStudent();
            }
        }

        foreach( $modules as $module )
        {
            $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                return $qcm->getIsOfficial();
            });

            $scores = [];
            foreach( $moduleOfficialQcms as $moduleOfficialQcm)
            {

                $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                foreach( $qcmInstances as $qcmInstance )
                {
                    if( in_array( $qcmInstance->getStudent(), $students) )
                    {
                        $result = $qcmInstance->getResult();
                        if( $result )
                        {
                            $scores[] = $result->getScore();
                        }
                    }
                }
            }
            $scoresOverFifty = array_filter($scores, function($score){ return $score >= 50; });
            $ratesByModule[] = [
                'title' => $module->getTitle(),
                'averageScore' => count($scores) > 0 ? array_sum($scores) / count($scores) : 0,
                'successRate' => count($scores) > 0 ? count($scoresOverFifty) / count($scores) : 0,
            ];
        }

        return $this->json( $ratesByModule );
    }

    #[Route('admin/stats/fetch/instructor-stacks-success-rate/{instructor}' ,name: 'admin_fetch_instructor_stacks_success_rate', methods: ['GET'])]
    public function fetchInstructorStacksSuccessRate(
        Instructor $instructor,
        LinkInstructorSessionModuleRepository $linkInstructorSessionModuleRepo,
        ModuleRepository $moduleRepo
    ) : JsonResponse
    {
        $ratesByStack = [];

        $linksInstructorSessionModule = $linkInstructorSessionModuleRepo->findBy( ['instructor' => $instructor] );

        $modules = [];
        $students = [];
        foreach( $linksInstructorSessionModule as $linkInstructorSessionModule )
        {
            $modules[] = $linkInstructorSessionModule->getModule();
            $linksSessionStudent = $linkInstructorSessionModule->getSession()->getLinksSessionStudent();
            foreach ( $linksSessionStudent as $linkSessionStudent )
            {
                $students[] = $linkSessionStudent->getStudent();
            }
        }

        $stacksNotUniq = array_map( function($module){
            return preg_replace('/[0-9]+/', '' ,$module->getTitle() );
        }, $modules );

        $stacks = array_unique( $stacksNotUniq );

        foreach( $stacks as $stack )
        {
            $stackModules = $moduleRepo->findAllModulesByBaseName($stack);

            $stackScores = [];
            $stackOverFiftyScores = [];
            foreach( $stackModules as $module )
            {
                $moduleOfficialQcms = $module->getQcms()->filter(function($qcm){
                    return $qcm->getIsOfficial();
                });

                $scores = [];
                foreach( $moduleOfficialQcms as $moduleOfficialQcm)
                {
                    $qcmInstances = $moduleOfficialQcm->getQcmInstances();
                    foreach( $qcmInstances as $qcmInstance )
                    {
                        if( in_array( $qcmInstance->getStudent() , $students ) )
                        {
                            $result = $qcmInstance->getResult();
                            if( $result )
                            {
                                $scores[] = $result->getScore();
                            }
                        }
                    }
                }
                $stackScores = array_merge( $stackScores, $scores );
                $stackOverFiftyScores = array_merge( $stackOverFiftyScores, array_filter($scores, function($score){ return $score >= 50; }) );
            }
            $ratesByStack[] = [
                'title' => $stack,
                'averageScore' => count($stackScores) > 0 ? array_sum($stackScores) / count($stackScores) : 0,
                'successRate' => count($stackScores) > 0 ? count($stackOverFiftyScores) / count($stackScores) : 0,
            ];
        }

        return $this->json( $ratesByStack );
    }
}
