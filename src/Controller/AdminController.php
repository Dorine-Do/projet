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
}
