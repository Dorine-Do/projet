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

    private Module $_module;
    private int $_difficulty;
    private string $_type;
    private $_user;

    public function __construct( QuestionRepository $questionRepo, Security $security )
    {
        $this->_questionRepo = $questionRepo;
        $this->_security = $security;
    }

    public function generateRandomQcm( Module $module, $user , UserRepository $userRepository , int $difficulty = 2 ,string $type = 'training'): Qcm
    {
        $this->_module = $module;
        $this->_difficulty = $difficulty;
        $this->_type = $type;
        $this->_user = $user;

        if( $this->_type === 'training' )
        {
            $title = 'QCM - Entrainement - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = false;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions();
        }
        if( $this->_type === 'retryBadge' )
        {
            $this->_difficulty = 2;
            $title = 'QCM - Retentative - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = true;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions();
        }
        if( $this->_type === 'official' )
        {
            $this->_difficulty = 2;
            $title = 'QCM - Officiel - ' . $module->getTitle();
            $isOfficial = true;
            $isPublic = true;
            $questions = $this->generateOfficialQcmQuestions();
        }

        $questionCache = $this->generateQuestionCache( $questions );

        $qcm = new Qcm();
        $qcm->setModule( $module );
        $qcm->setAuthor( $userRepository->find($this->_user->getId()) );
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

    private function generateTrainingQcmQuestions(): array
    {
        $questionsPool = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $this->_module
        ]);

        $easyQuestionsPool = array_filter($questionsPool, function( $questionFromMainPool ){
            return $questionFromMainPool->getDifficulty()->value === 1;
        });

        $mediumQuestionsPool = array_filter($questionsPool, function( $questionFromMainPool ){
            return $questionFromMainPool->getDifficulty()->value === 2;
        });

        $difficultQuestionsPool = array_filter($questionsPool, function( $questionFromMainPool ){
            return $questionFromMainPool->getDifficulty()->value === 3;
        });

        $questionsQuantityByDifficulty = $this->calcQuestionsNumberToPickByDifficulty();

        $pickedEasyQuestions = $this->pickQuestionsInPool( $questionsQuantityByDifficulty['easy'], $easyQuestionsPool );
        $pickedMediumQuestions = $this->pickQuestionsInPool( $questionsQuantityByDifficulty['medium'], $mediumQuestionsPool );
        $pickedDifficultQuestions = $this->pickQuestionsInPool( $questionsQuantityByDifficulty['difficult'], $difficultQuestionsPool );

        $pickedQuestions = array_merge( $pickedEasyQuestions, $pickedMediumQuestions, $pickedDifficultQuestions );

        shuffle($pickedQuestions);

        return $pickedQuestions;
    }

    private function generateOfficialQcmQuestions(): array
    {
        $mandatoryQuestionsPool = $this->_questionRepo->findBy([
            'isMandatory' => true,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $this->_module
        ]);
        $nonMandatoryQuestionsPool = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $this->_module
        ]);

        $nonMandatoryEasyQuestionsPool = array_filter($nonMandatoryQuestionsPool, function( $nonMandatoryQuestion ){
            return $nonMandatoryQuestion->getDifficulty()->value === 1;
        });

        $nonMandatoryMediumQuestionsPool =  array_filter($nonMandatoryQuestionsPool, function( $nonMandatoryQuestion ){
            return $nonMandatoryQuestion->getDifficulty()->value === 2;
        });

        $nonMandatoryDifficultQuestionsPool = array_filter($nonMandatoryQuestionsPool, function( $nonMandatoryQuestion ){
            return $nonMandatoryQuestion->getDifficulty()->value === 3;
        });

        $mandatoryQuestionsToPickNbr = min( count( $mandatoryQuestionsPool ), $this->_officialQcmQuestionQuantity);

        $pickedMandatoryQuestions = $this->pickQuestionsInPool( $mandatoryQuestionsToPickNbr, $mandatoryQuestionsPool );

        $nonMandatoryPickedEasyQuestions = [];
        $nonMandatoryPickedMediumQuestions = [];
        $nonMandatoryPickedDifficultQuestions = [];
        if( $mandatoryQuestionsToPickNbr < $this->_officialQcmQuestionQuantity )
        {
            $remainingQuestionsQuantityToPick = $this->_officialQcmQuestionQuantity - $mandatoryQuestionsToPickNbr;

            $keepGoing = true;

            while( $keepGoing )
            {
                $nonMandatoryMediumQuestionsNbr = ceil( mt_rand( $this->_officialQcmQuestionQuantity / 2, $this->_officialQcmQuestionQuantity ) ) - $mandatoryQuestionsToPickNbr;
                $nonMandatoryEasyQuestionsNbr = ceil( mt_rand( ( $this->_officialQcmQuestionQuantity / 4 )  - ( $nonMandatoryMediumQuestionsNbr - $this->_officialQcmQuestionQuantity / 2 ) , $this->_officialQcmQuestionQuantity - $nonMandatoryMediumQuestionsNbr) );
                $nonMandatoryDifficultQuestionsNbr = $remainingQuestionsQuantityToPick - $nonMandatoryMediumQuestionsNbr - $nonMandatoryEasyQuestionsNbr;

                if( $nonMandatoryEasyQuestionsNbr < 2 * $nonMandatoryMediumQuestionsNbr && 2 * $nonMandatoryMediumQuestionsNbr > 3 * $nonMandatoryDifficultQuestionsNbr )
                {
                    $keepGoing = false;
                }
            }

            $nonMandatoryPickedEasyQuestions = $this->pickQuestionsInPool( $nonMandatoryEasyQuestionsNbr, $nonMandatoryEasyQuestionsPool );
            $nonMandatoryPickedMediumQuestions = $this->pickQuestionsInPool( $nonMandatoryMediumQuestionsNbr, $nonMandatoryMediumQuestionsPool );
            $nonMandatoryPickedDifficultQuestions = $this->pickQuestionsInPool( $nonMandatoryDifficultQuestionsNbr, $nonMandatoryDifficultQuestionsPool );
        }

        $pickedQuestions = array_merge( $pickedMandatoryQuestions , $nonMandatoryPickedEasyQuestions, $nonMandatoryPickedMediumQuestions, $nonMandatoryPickedDifficultQuestions );

        shuffle($pickedQuestions);

        return $pickedQuestions;
    }

    private function pickQuestionsInPool( int $quantityToPick, array $basePool ) : array
    {
        $pickedQuestions = [];
        for( $eq = 0; $eq < $quantityToPick; $eq++ )
        {
            if( count($pickedQuestions) > 0 )
            {
                foreach( $pickedQuestions as $alreadyPickedQuestion )
                {
                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $basePool );
                    unset( $basePool[$questionToNotReuseIndex] );
                }
            }
            $recalculatedPool = $basePool;
            $pickedQuestions[] = $recalculatedPool[ array_rand($recalculatedPool) ];
        }

        return $pickedQuestions;
    }

    private function calcQuestionsNumberToPickByDifficulty() : array
    {
        $totalQuestions = $this->_trainingQcmQuestionQuantity;
        if( $this->_type === 'official' )
        {
            $totalQuestions = $this->_officialQcmQuestionQuantity;
        }

        $questionsNbrByDifficulty = [
            'easy' => 0,
            'medium' => 0,
            'difficult' => 0
        ];

        $keepGoing = true;

        while( $keepGoing )
        {
            switch( $this->_difficulty )
            {
                case 1:
                    $easyQuestionsNbr = ceil( mt_rand( $totalQuestions * 5 / 6, $totalQuestions) );
                    $mediumQuestionsNbr = ceil( mt_rand(  ( $totalQuestions / 6 - ($easyQuestionsNbr - $totalQuestions * 5 / 6) ) / 2, $totalQuestions - $easyQuestionsNbr) );
                    $difficultQuestionsNbr = $totalQuestions - $easyQuestionsNbr - $mediumQuestionsNbr;
                    if( $easyQuestionsNbr > 2 * $mediumQuestionsNbr && $easyQuestionsNbr > 3 * $difficultQuestionsNbr )
                    {
                        $questionsNbrByDifficulty = [
                            'easy' => $easyQuestionsNbr,
                            'medium' => $mediumQuestionsNbr,
                            'difficult' => $difficultQuestionsNbr
                        ];
                        $keepGoing = false;
                    }
                    break;
                case 2:
                    $mediumQuestionsNbr = ceil( mt_rand( $totalQuestions / 2, $totalQuestions) );
                    $easyQuestionsNbr = ceil( mt_rand( ( $totalQuestions / 4 )  - ( $mediumQuestionsNbr - $totalQuestions / 2 ) , $totalQuestions - $mediumQuestionsNbr) );
                    $difficultQuestionsNbr = $totalQuestions - $easyQuestionsNbr - $mediumQuestionsNbr;
                    if( $easyQuestionsNbr < 2 * $mediumQuestionsNbr && 2 * $mediumQuestionsNbr > 3 * $difficultQuestionsNbr )
                    {
                        $questionsNbrByDifficulty = [
                            'easy' => $easyQuestionsNbr,
                            'medium' => $mediumQuestionsNbr,
                            'difficult' => $difficultQuestionsNbr
                        ];
                        $keepGoing = false;
                    }
                    break;
                case 3:
                    $difficultQuestionsNbr = ceil( mt_rand($totalQuestions / 3, $totalQuestions) );
                    $easyQuestionsNbr = mt_rand( ( ( $totalQuestions * 2 / 3 ) - ($difficultQuestionsNbr - $totalQuestions / 3) ) * 5 / 6  , $totalQuestions - $difficultQuestionsNbr);
                    $mediumQuestionsNbr = $totalQuestions - $difficultQuestionsNbr - $easyQuestionsNbr;
                    if( $easyQuestionsNbr < 3 * $difficultQuestionsNbr && 2 * $mediumQuestionsNbr < 3 * $difficultQuestionsNbr )
                    {
                        $questionsNbrByDifficulty = [
                            'easy' => $easyQuestionsNbr,
                            'medium' => $mediumQuestionsNbr,
                            'difficult' => $difficultQuestionsNbr
                        ];
                        $keepGoing = false;
                    }
                    break;
            }
        }

        return $questionsNbrByDifficulty;
    }
}