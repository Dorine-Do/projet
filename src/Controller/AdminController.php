<?php

namespace App\Controller;

use App\Entity\Main\Admin;
use App\Entity\Main\Instructor;
use App\Entity\Main\Session;
use App\Entity\Main\Student;
use App\Entity\Main\User;
use App\Form\RegistrationFormType;
use App\Repository\ModuleRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
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
        UserRepository $userRepo,
        Request $request,
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
        return $this->json( $user, 200, [],['groups' => 'user:read'] );
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

    // TODO: Probablement à supprimer
    #[Route('/admin/new-user', name: 'app_new_user')]
    public function newUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setCreatedAt(new \DateTime());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);

            if( in_array( 'student', $user->getRoles() ) || in_array( 'instructor', $user->getRoles() ) || in_array( 'admin', $user->getRoles() )  )
            {
                $student = new Student();
                $student->setIdModule( $user->getIdMoodle() );
                $student->setFirstName( $user->getFirstName() );
                $student->setLastName( $user->getLastName() );
                $student->setBirthDate( $user->getBirthDate() );
                $student->setMail3wa( $user->getFirstName() . '.' . $user->getLastName() . '@3wa.io' );
                $student->setCreatedAtValue();

                $entityManager->persist($student);

                if( in_array( 'instructor', $user->getRoles() ) || in_array( 'admin', $user->getRoles() ) ) {
                    $instructor = new Instructor();
                    $instructor->setFirstName( $user->getFirstName() );
                    $instructor->setLastName( $user->getLastName() );
                    $instructor->setBirthDate( $user->getBirthDate() );
                    $instructor->setPhoneNumber( '0600000000' );
                    $instructor->setPassword( $user->getPassword() );
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

    // TODO: à supprimer d`s que le test est concluant ou que la f`con de faire est abandonée
    #[Route('admin/test', name: 'admin_test')]
    public function adminTest( ManagerRegistry $doctrine ): Response
    {
//        $suiviSessions = $this->getDataFromSuivi( 'SELECT * FROM sessions WHERE id < 4' );
//        $sessions = [];
//        foreach ($suiviSessions as $suiviSession)
//        {
//            $params = [
//                'id' => $suiviSession['id']
//            ];
//            $sessionStart = $this->getDataFromSuivi( 'SELECT DISTINCT MIN(date) FROM daily WHERE id_session = :id GROUP BY id_session', $params );
//            $explodedStartDate = explode('-', $sessionStart[0]['MIN(date)']);
//
//            $sessions[] = [
//                'id' => $suiviSession['id'],
//                'name' => $suiviSession['name'],
//                'school_year' => $explodedStartDate[0],
//                'created_at' => new \DateTime(),
//                'updated_at' => new \DateTime(),
//            ];
//        }
//
//
//        $moduleByName = [];
//        foreach ($sessions as $session)
//        {
//            $params = [
//                'id' => $session["id"]
//            ];
//            $modulesSql = "SELECT DISTINCT
//                sessions.name as session_name,
//                sessions.id as session_id,
//                modules.name as module_name,
//                modules.id as module_id,
//                MIN(date) as start_date,
//                MAX(date) as end_date,
//                COUNT(date) as duration
//                FROM users
//                LEFT JOIN daily ON daily.id_user = users.id
//                LEFT JOIN modules ON modules.id = daily.id_module
//                LEFT JOIN categories ON categories.id = modules.id_category
//                LEFT JOIN sessions ON sessions.id = daily.id_session
//                WHERE sessions.id = :id
//                GROUP BY modules.id";
//
//            $suiviModules = $this->getDataFromSuivi($modulesSql, $params);
//            $moduleByName = [];
//            foreach($suiviModules as $suiviModule)
//            {
//                $explodedModuleName = explode('.', $suiviModule['module_name']);
//                $moduleName = $explodedModuleName[0];
//                if( !array_key_exists($moduleName, $moduleByName) )
//                {
//                    $moduleByName[$moduleName] = [];
//                }
//                $moduleByName[$moduleName][] = $suiviModule;
//            }
//        }
//
//        $modules = [];
//        foreach( $moduleByName as $name => $submodules )
//        {
//            $moduleDuration = 0;
//            foreach( $submodules as $submodule  )
//            {
//                $moduleDuration += $submodule['duration'];
//            }
//
//            $weeks = ceil( $moduleDuration / 5 );
//
//            $modules[] = [
//                'name' => $name,
//                'weeks' => $weeks,
//                'created_at' => new \DateTime(),
//                'updated_at' => new \DateTime(),
//            ];
//        }

        $instructorsAndSessionByModule = $this->getDataFromSuivi('SELECT DISTINCT
                users.id as instructor_id,
                modules.id as module_id,
                sessions.id as session_id,
                modules.name as module_name
                FROM users
                LEFT JOIN daily ON daily.id_user = users.id
                LEFT JOIN modules ON modules.id = daily.id_module
                LEFT JOIN categories ON categories.id = modules.id_category
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id <= 3');

        $instructorsAndSessionByModule = array_map(function($item){

            $explodedName = explode('.', $item['module_name']);
            $moduleName = $explodedName[0];

            return [
                'instructor_id' => $item['instructor_id'],
                'session_id' => $item['session_id'],
                'module_id' => $item['module_id'],
                'module_name' => $moduleName,
            ];
        }, $instructorsAndSessionByModule);

        $isms = [];

        foreach ( $instructorsAndSessionByModule as $ism )
        {
            if( !in_array( $ism, $isms ) )
            {
                $isms[] = $ism;
            }
        }

//        for($i = 0; $i < count($instructorsAndSessionByModule); $i++)
//        {
//            $testInstructorId = in_array( $instructorsAndSessionByModule[$i]['instructor_id'], $isms );
//            $testSessionId = in_array( $instructorsAndSessionByModule[$i]['session_id'], $isms );
//            $testModuleName = in_array( $instructorsAndSessionByModule[$i]['module_name'], $isms );
//
//            if( !$testInstructorId && !$testSessionId && !$testModuleName )
//            {
//                $isms[] = [
//                    'instructor_id' => $instructorsAndSessionByModule[$i]['instructor_id'],
//                    'session_id' => $instructorsAndSessionByModule[$i]['session_id'],
//                    'module_name' => $instructorsAndSessionByModule[$i]['module_name'],
//                ];
//            }
//        }

        $result = $isms;

        return $this->render('admin/test.html.twig', [
            'result' => $result,
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
}
