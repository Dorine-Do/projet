<?php

namespace App\Helpers;

use App\Entity\Main\Module;
use App\Entity\Main\Qcm;
use App\Repository\InstructorRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class QcmGeneratorHelper
{
    private QuestionRepository $_questionRepo;
    private Security $_security;
    private int $_trainingQcmQuestionQuantity = 20;
    private int $_officialQcmQuestionQuantity = 42;

    public function __construct( QuestionRepository $questionRepo, Security $security )
    {
        $this->_questionRepo = $questionRepo;
        $this->_security = $security;
    }
    public function generateRandomQcm( Module $module, $user , UserRepository $userRepository , int $difficulty ,string $type = 'training'): Qcm
    {
        if( $type === 'training' )
        {
            $title = 'QCM - Entrainement - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = false;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions( $module );
        }
        if( $type === 'retryBadge' )
        {
            $title = 'QCM - Retentative - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = true;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions( $module );
        }
        if( $type === 'official' )
        {
            $title = 'QCM - Officiel - ' . $module->getTitle();
            $isOfficial = true;
            $isPublic = true;
            $questions = $this->generateOfficialQcmQuestions( $module );
        }

        $questionCache = $this->generateQuestionCache( $questions );

        $qcm = new Qcm();
        $qcm->setModule( $module );
        $qcm->setAuthor( $userRepository->find($user->getId()) );
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
        $questionsPool = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $module
        ]);

        $pickedQuestions = [];
        for( $q = 0; $q < $this->_trainingQcmQuestionQuantity; $q++ )
        {
            if( count($pickedQuestions) > 0 )
            {
                foreach( $pickedQuestions as $alreadyPickedQuestion )
                {
                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $questionsPool );
                    unset( $questionsPool[$questionToNotReuseIndex] );
                }
            }
            $recalcPool = $questionsPool;
            $pickedQuestions[] = $recalcPool[ array_rand($recalcPool) ];
        }
        return $pickedQuestions;
    }

    private function generateOfficialQcmQuestions( Module $module ): array
    {
        $mandatoryQuestionsPool = $this->_questionRepo->findBy([
            'isMandatory' => true,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $module
        ]);
        $nonMandatoryQuestionsPool = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $module
        ]);

        $mandatoryQuestionsToPickNbr = min( count( $mandatoryQuestionsPool ), $this->_officialQcmQuestionQuantity);
        $nonMandatoryQuestionsToPickNbr = $this->_officialQcmQuestionQuantity - $mandatoryQuestionsToPickNbr;

        $pickedQuestions = [];
        for( $mq = 0; $mq < $mandatoryQuestionsToPickNbr; $mq++ )
        {
            if( count($pickedQuestions) > 0 )
            {
                foreach( $pickedQuestions as $alreadyPickedQuestion )
                {
                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $mandatoryQuestionsPool );
                    unset( $mandatoryQuestionsPool[$questionToNotReuseIndex] );
                }
            }
            $recalcMandatoryPool = $mandatoryQuestionsPool;
            $pickedQuestions[] = $recalcMandatoryPool[ array_rand( $recalcMandatoryPool ) ];
        }
        for( $nmq = 0; $nmq < $nonMandatoryQuestionsToPickNbr; $nmq++ )
        {
            if( count($pickedQuestions) > 0 )
            {
                foreach( $pickedQuestions as $alreadyPickedQuestion )
                {
                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $nonMandatoryQuestionsPool );
                    unset( $nonMandatoryQuestionsPool[$questionToNotReuseIndex] );
                }
            }
            $recalcNonMandatoryPool = $nonMandatoryQuestionsPool;
            $pickedQuestions[] = $recalcNonMandatoryPool[ array_rand( $recalcNonMandatoryPool ) ];
        }
        return $pickedQuestions;
    }

    public function generateQuestionCache( array $questions ): array
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
                'id'         => $question->getId(),
                'wording'    => $question->getWording(),
                'isMultiple' => $question->getIsMultiple(),
                'difficulty' => $question->getDifficulty(),
                'proposals'  => $proposalsCache
            ];
        }
        return $questionsCache;
    }
}
