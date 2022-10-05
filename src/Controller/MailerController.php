<?php

    namespace App\Controller;

    use App\Entity\Main\User;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\Routing\Annotation\Route;

    class MailerController extends AbstractController
    {

        public function sendEmail(MailerInterface $mailer, User $user): Response
        {

        }

    }