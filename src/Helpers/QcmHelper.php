<?php

namespace App\Helpers;

use App\Entity\Module;
use App\Entity\Qcm;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Security;

class QcmHelper
{
    private QuestionRepository $_questionRepository;
    private UserRepository $_userRepository;
    private Security $_security;
    private ObjectManager $_manager;

    public function __construct( QuestionRepository $questionRepository, UserRepository $userRepository,Security $security, ObjectManager $manager )
    {
        $this->_questionRepository = $questionRepository;
        $this->_userRepository = $userRepository;
        $this->_security = $security;
        $this->_manager = $manager;
    }

    public function generateRandomQcm( Module $module, bool $isTraining = true, int $difficulty = 2 ): Qcm
    {
        if( $isTraining )
        {
            $title = 'QCM - Entrainement - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = false;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions( $module );
        }
        else
        {
            $title = 'QCM - Officiel - ' . $module->getTitle();
            $isOfficial = true;
            $isPublic = true;
            $questions = $this->generateOfficialQcmQuestions( $module );
        }

        $questionCache = $this->generateQuestionCache( $questions );

        $qcm = new Qcm();
        $qcm->setModule( $module );
        $qcm->setAuthor( $this->_userRepository->findOneBy( ['email' => $this->_security->getUser()->getUserIdentifier()] ) );
        $qcm->setTitle( $title );
        $qcm->setDifficulty( $difficulty );
        $qcm->setIsOfficial( $isOfficial );
        $qcm->setIsEnabled( true );
        $qcm->setIsPublic( $isPublic );
        $qcm->setQuestionsCache( $questionCache );
        $qcm->setCreatedAtValue();
        $qcm->setUpdateAtValue();

        return $qcm;
    }

    private function generateTrainingQcmQuestions( Module $module ): array
    {
        $questionsPool = $this->_questionRepository->findBy( ['isMandatory' => false, 'isOfficial' => true, 'isEnabled' => true, 'module' => $module] );
        $pickedQuestions = [];
        for( $q = 0; $q < 20; $q++ )
        {
            $pickedQuestion = $questionsPool[ array_rand($questionsPool) ];
            while( in_array( $pickedQuestion, $pickedQuestions ) )
            {
                $pickedQuestion = $questionsPool[ array_rand($questionsPool) ];
            }
            $pickedQuestions[] = $pickedQuestion;
        }
        return $pickedQuestions;
    }

    private function generateOfficialQcmQuestions( Module $module ): array
    {
        $mandatoryQuestionsPool = $this->_questionRepository->findBy( ['isMandatory' => true, 'isOfficial' => true, 'isEnabled' => true, 'module' => $module] );
        $nonMandatoryQuestionsPool = $this->_questionRepository->findBy( ['isMandatory' => false, 'isOfficial' => true, 'isEnabled' => true, 'module' => $module] );
        $pickedQuestions = [];
        for( $mq = 0; $mq < 10; $mq++ )
        {
            $pickedQuestion = $mandatoryQuestionsPool[ array_rand( $mandatoryQuestionsPool ) ];
            while( in_array( $pickedQuestion, $pickedQuestions ) )
            {
                $pickedQuestion = $mandatoryQuestionsPool[ array_rand( $mandatoryQuestionsPool ) ];
            }
            $pickedQuestions[] = $pickedQuestion;
        }
        for( $nmq = 0; $nmq < 32; $nmq++ )
        {
            $pickedQuestion = $nonMandatoryQuestionsPool[ array_rand( $nonMandatoryQuestionsPool ) ];
            while( in_array( $pickedQuestion, $pickedQuestions ) )
            {
                $pickedQuestion = $nonMandatoryQuestionsPool[ array_rand( $nonMandatoryQuestionsPool ) ];
            }
            $pickedQuestions[] = $pickedQuestion;
        }
        return $pickedQuestions;
    }

    private function generateQuestionCache( array $questions ): array
    {
        $questionsCache = [];
        foreach( $questions as $question )
        {
            $questionProposals = $question->getProposals();
            $proposalsCache = [];
            foreach( $questionProposals as $questionProposal )
            {
                $proposalsCache[] = [
                    'id'                => $questionProposal->getId(),
                    'wording'           => $questionProposal->getWording(),
                    'isCorrectAnswer'   => $questionProposal->getIsCorrectAnswer(),
                ];
            }
            $questionsCache[] = [
                [
                    'id'         => $question->getId(),
                    'wording'    => $question->getWording(),
                    'isMultiple' => $question->getIsMultiple(),
                    'proposals'  => $proposalsCache
                ]
            ];
        }
        return $questionsCache;
    }
}
