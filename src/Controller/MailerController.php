<?php

    namespace App\Controller;

    use App\Entity\Main\User;
    use App\Repository\InstructorRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\Routing\Annotation\Route;

    class MailerController extends AbstractController
    {
        #[Route('bug-report/{message}',name:'bug_report',methods:['GET'])]
        public function bugReport(
            MailerInterface $mailer,
            $message,
            InstructorRepository $instructorRepository
        ):Response
        {
            $user=$instructorRepository->find(1);
            if($message){
                $email = (new Email())
                    ->from($user->getEmail())
                    ->to('evan.collebrusco@3wa.io')
                    ->subject('Time for Symfony Mailer!')
                    ->html($message);

                $mailer->send($email);
            }


            return $this->render('instructor/sendMail.html.twig', [

            ]);

        }



    }