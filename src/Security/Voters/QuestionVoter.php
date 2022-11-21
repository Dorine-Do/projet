<?php

namespace App\Security\Voters;

use App\Entity\Main\Admin;
use App\Entity\Main\Instructor;
use App\Entity\Main\Question;
use App\Entity\Main\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class QuestionVoter extends Voter
{

    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on `Qcm` objects
        if (!$subject instanceof Question) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ( !($user instanceof Admin || $user instanceof Instructor) ) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Question $question */
        $Question = $subject;

        return match($attribute) {
            self::EDIT => $this->canEditQuestion( $user ),
            self::DELETE => $this->canDeleteQuestion( $user ),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEditQuestion(UserInterface $user): bool
    {
        return  $user instanceof Admin || $user instanceof Instructor;
    }

    private function canDeleteQuestion(UserInterface $user): bool
    {
        return  $user instanceof Admin || $user instanceof Instructor;
    }
}