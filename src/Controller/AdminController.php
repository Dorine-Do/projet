<?php

namespace App\Controller;

use App\Entity\Main\Admin;
use App\Entity\Main\Instructor;
use App\Entity\Main\Student;
use App\Entity\Main\User;
use App\Form\RegistrationFormType;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
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
}
