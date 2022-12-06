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

    public function generateRandomQcm( Module $module, $user , UserRepository $userRepository , int $difficulty = 2 ,string $type = 'training'): Qcm
    {
        if( $type === 'training' )
        {
            $title = 'QCM - Entrainement - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = false;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions( $module, $difficulty );
        }
        if( $type === 'retryBadge' )
        {
            $title = 'QCM - Retentative - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
            $isOfficial = true;
            $isPublic = false;
            $questions = $this->generateTrainingQcmQuestions( $module, 2 );
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

    private function generateTrainingQcmQuestions( Module $module, int $difficulty ): array
    {
        $questionsPool = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => true,
            'isEnabled' => true,
            'module' => $module
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

        $questionsQuantityByDifficulty = $this->calcQuestionsNumberToPickByDifficulty( $difficulty, $this->_trainingQcmQuestionQuantity );

        $pickedEasyQuestions = [];
        for( $eq = 0; $eq < $questionsQuantityByDifficulty['easy']; $eq++ )
        {
            if( count($pickedEasyQuestions) > 0 )
            {
                foreach( $pickedEasyQuestions as $alreadyPickedEasyQuestion )
                {
                    $easyQuestionToNotReuseIndex = array_search( $alreadyPickedEasyQuestion, $easyQuestionsPool );
                    unset( $easyQuestionsPool[$easyQuestionToNotReuseIndex] );
                }
            }
            $recalcEasyPool = $questionsPool;
            $pickedEasyQuestions[] = $recalcEasyPool[ array_rand($recalcEasyPool) ];
        }

        $pickedMediumQuestions = [];
        for( $eq = 0; $eq < $questionsQuantityByDifficulty['medium']; $eq++ )
        {
            if( count($pickedMediumQuestions) > 0 )
            {
                foreach( $pickedMediumQuestions as $alreadyPickedMediumQuestion )
                {
                    $mediumQuestionToNotReuseIndex = array_search( $alreadyPickedMediumQuestion, $mediumQuestionsPool );
                    unset( $mediumQuestionsPool[$mediumQuestionToNotReuseIndex] );
                }
            }
            $recalcMediumPool = $questionsPool;
            $pickedMediumQuestions[] = $recalcMediumPool[ array_rand($recalcMediumPool) ];
        }

        $pickedDifficultQuestions = [];
        for( $eq = 0; $eq < $questionsQuantityByDifficulty['difficult']; $eq++ )
        {
            if( count($pickedDifficultQuestions) > 0 )
            {
                foreach( $pickedDifficultQuestions as $alreadyPickedDifficultQuestion )
                {
                    $difficultQuestionToNotReuseIndex = array_search( $alreadyPickedDifficultQuestion, $difficultQuestionsPool );
                    unset( $difficultQuestionsPool[$difficultQuestionToNotReuseIndex] );
                }
            }
            $recalcDifficultPool = $questionsPool;
            $pickedDifficultQuestions[] = $recalcDifficultPool[ array_rand($recalcDifficultPool) ];
        }

        $pickedQuestions = array_merge( $pickedEasyQuestions, $pickedMediumQuestions, $pickedDifficultQuestions );

        shuffle($pickedQuestions);

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
        $nonMandatoryQuestionsToPickNbr = $this->_officialQcmQuestionQuantity - $mandatoryQuestionsToPickNbr;

        $pickedMandatoryQuestions = [];
        for( $mq = 0; $mq < $mandatoryQuestionsToPickNbr; $mq++ )
        {
            if( count($pickedMandatoryQuestions) > 0 )
            {
                foreach( $pickedMandatoryQuestions as $alreadyPickedQuestion )
                {
                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $mandatoryQuestionsPool );
                    unset( $mandatoryQuestionsPool[$questionToNotReuseIndex] );
                }
            }
            $recalcMandatoryPool = $mandatoryQuestionsPool;
            $pickedMandatoryQuestions[] = $recalcMandatoryPool[ array_rand( $recalcMandatoryPool ) ];
        }

        $nonMandatoryPickedEasyQuestions = [];
        $nonMandatoryPickedMediumQuestions = [];
        $nonMandatoryPickedDifficultQuestions = [];
        if( $mandatoryQuestionsToPickNbr < $this->_officialQcmQuestionQuantity )
        {
            $remainingQuestionsQuantityToPick = $this->_officialQcmQuestionQuantity - $mandatoryQuestionsToPickNbr;

            $keepGoing = true;

            while( $keepGoing )
            {
                $nonMandatoryMediumQuestionsNbr = ceil( mt_rand( $this->_officialQcmQuestionQuantity * (1/2), $this->_officialQcmQuestionQuantity ) ) - $mandatoryQuestionsToPickNbr;
                $nonMandatoryEasyQuestionsNbr = mt_rand( 1, $remainingQuestionsQuantityToPick - $nonMandatoryMediumQuestionsNbr);
                $nonMandatoryDifficultQuestionsNbr = $remainingQuestionsQuantityToPick - $nonMandatoryMediumQuestionsNbr - $nonMandatoryEasyQuestionsNbr;

                if( $nonMandatoryEasyQuestionsNbr < 2 * $nonMandatoryMediumQuestionsNbr && 2 * $nonMandatoryMediumQuestionsNbr > 3 * $nonMandatoryDifficultQuestionsNbr )
                {
                    $keepGoing = false;
                }
            }

            $nonMandatoryPickedEasyQuestions = [];
            for( $eq = 0; $eq < $nonMandatoryEasyQuestionsNbr; $eq++ )
            {
                if( count($nonMandatoryPickedEasyQuestions) > 0 )
                {
                    foreach( $nonMandatoryPickedEasyQuestions as $alreadyNonMandatoryPickedEasyQuestion )
                    {
                        $easyQuestionToNotReuseIndex = array_search( $alreadyNonMandatoryPickedEasyQuestion, $nonMandatoryEasyQuestionsPool );
                        unset( $nonMandatoryEasyQuestionsPool[$easyQuestionToNotReuseIndex] );
                    }
                }
                $recalcEasyPool = $nonMandatoryEasyQuestionsPool;
                $nonMandatoryPickedEasyQuestions[] = $recalcEasyPool[ array_rand($recalcEasyPool) ];
            }

            $nonMandatoryPickedMediumQuestions = [];
            for( $eq = 0; $eq < $nonMandatoryMediumQuestionsNbr; $eq++ )
            {
                if( count($nonMandatoryPickedMediumQuestions) > 0 )
                {
                    foreach( $nonMandatoryPickedMediumQuestions as $alreadyNonMandatoryPickedMediumQuestion )
                    {
                        $mediumQuestionToNotReuseIndex = array_search( $alreadyNonMandatoryPickedMediumQuestion, $nonMandatoryMediumQuestionsPool );
                        unset( $nonMandatoryMediumQuestionsPool[$mediumQuestionToNotReuseIndex] );
                    }
                }
                $recalcMediumPool = $nonMandatoryMediumQuestionsPool;
                $nonMandatoryPickedMediumQuestions[] = $recalcMediumPool[ array_rand($recalcMediumPool) ];
            }

            $nonMandatoryPickedDifficultQuestions = [];
            for( $eq = 0; $eq < $nonMandatoryDifficultQuestionsNbr; $eq++ )
            {
                if( count($nonMandatoryPickedDifficultQuestions) > 0 )
                {
                    foreach( $nonMandatoryPickedDifficultQuestions as $alreadyNonMandatoryPickedDifficultQuestion )
                    {
                        $difficultQuestionToNotReuseIndex = array_search( $alreadyNonMandatoryPickedDifficultQuestion, $nonMandatoryDifficultQuestionsPool );
                        unset( $nonMandatoryDifficultQuestionsPool[$difficultQuestionToNotReuseIndex] );
                    }
                }
                $recalcDifficultPool = $nonMandatoryDifficultQuestionsPool;
                $nonMandatoryPickedDifficultQuestions[] = $recalcDifficultPool[ array_rand($recalcDifficultPool) ];
            }
        }

        $pickedQuestions = array_merge( $pickedMandatoryQuestions , $nonMandatoryPickedEasyQuestions, $nonMandatoryPickedMediumQuestions, $nonMandatoryPickedDifficultQuestions );

        shuffle($pickedQuestions);

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

    public function calcQuestionsNumberToPickByDifficulty( int $wantedDifficulty, $totalQuestions ) : array
    {
        $questionsNbrByDifficulty = [
            'easy' => 0,
            'medium' => 0,
            'difficult' => 0
        ];

        $keepGoing = true;

        while( $keepGoing )
        {
            switch( $wantedDifficulty )
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

//    public function generateRandomQcm( Module $module, $user , UserRepository $userRepository , int $difficulty = 2 ,string $type = 'training'): Qcm
//    {
//        if( $type === 'training' )
//        {
//            $title = 'QCM - Entrainement - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
//            $isOfficial = false;
//            $isPublic = false;
//            $questions = $this->generateTrainingQcmQuestions( $module );
//        }
//        if( $type === 'retryBadge' )
//        {
//            $title = 'QCM - Retentative - ' . $module->getTitle() . ' - ' . date('Ymd H:i');
//            $isOfficial = true;
//            $isPublic = false;
//            $questions = $this->generateTrainingQcmQuestions( $module );
//        }
//        if( $type === 'official' )
//        {
//            $title = 'QCM - Officiel - ' . $module->getTitle();
//            $isOfficial = true;
//            $isPublic = true;
//            $questions = $this->generateOfficialQcmQuestions( $module );
//        }
//
//        $questionCache = $this->generateQuestionCache( $questions );
//
//        $qcm = new Qcm();
//        $qcm->setModule( $module );
//        $qcm->setAuthor( $userRepository->find($user->getId()) );
//        $qcm->setTitle( $title );
//        $qcm->setDifficulty( $difficulty );
//        $qcm->setIsOfficial( $isOfficial );
//        $qcm->setIsEnabled( true );
//        $qcm->setIsPublic( $isPublic );
//        $qcm->setQuestionsCache( $questionCache );
//        $qcm->setCreatedAtValue();
//        $qcm->setUpdateAtValue();
//
//        return $qcm;
//    }
//
//    private function generateTrainingQcmQuestions( Module $module ): array
//    {
//        $questionsPool = $this->_questionRepo->findBy([
//            'isMandatory' => false,
//            'isOfficial' => true,
//            'isEnabled' => true,
//            'module' => $module
//        ]);
//
//        $pickedQuestions = [];
//        for( $q = 0; $q < $this->_trainingQcmQuestionQuantity; $q++ )
//        {
//            if( count($pickedQuestions) > 0 )
//            {
//                foreach( $pickedQuestions as $alreadyPickedQuestion )
//                {
//                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $questionsPool );
//                    unset( $questionsPool[$questionToNotReuseIndex] );
//                }
//            }
//            $recalcPool = $questionsPool;
//            $pickedQuestions[] = $recalcPool[ array_rand($recalcPool) ];
//        }
//        return $pickedQuestions;
//    }
//
//    private function generateOfficialQcmQuestions( Module $module ): array
//    {
//        $mandatoryQuestionsPool = $this->_questionRepo->findBy([
//            'isMandatory' => true,
//            'isOfficial' => true,
//            'isEnabled' => true,
//            'module' => $module
//        ]);
//        $nonMandatoryQuestionsPool = $this->_questionRepo->findBy([
//            'isMandatory' => false,
//            'isOfficial' => true,
//            'isEnabled' => true,
//            'module' => $module
//        ]);
//
//        $mandatoryQuestionsToPickNbr = min( count( $mandatoryQuestionsPool ), $this->_officialQcmQuestionQuantity);
//        $nonMandatoryQuestionsToPickNbr = $this->_officialQcmQuestionQuantity - $mandatoryQuestionsToPickNbr;
//
//        $pickedQuestions = [];
//        for( $mq = 0; $mq < $mandatoryQuestionsToPickNbr; $mq++ )
//        {
//            if( count($pickedQuestions) > 0 )
//            {
//                foreach( $pickedQuestions as $alreadyPickedQuestion )
//                {
//                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $mandatoryQuestionsPool );
//                    unset( $mandatoryQuestionsPool[$questionToNotReuseIndex] );
//                }
//            }
//            $recalcMandatoryPool = $mandatoryQuestionsPool;
//            $pickedQuestions[] = $recalcMandatoryPool[ array_rand( $recalcMandatoryPool ) ];
//        }
//        for( $nmq = 0; $nmq < $nonMandatoryQuestionsToPickNbr; $nmq++ )
//        {
//            if( count($pickedQuestions) > 0 )
//            {
//                foreach( $pickedQuestions as $alreadyPickedQuestion )
//                {
//                    $questionToNotReuseIndex = array_search( $alreadyPickedQuestion, $nonMandatoryQuestionsPool );
//                    unset( $nonMandatoryQuestionsPool[$questionToNotReuseIndex] );
//                }
//            }
//            $recalcNonMandatoryPool = $nonMandatoryQuestionsPool;
//            $pickedQuestions[] = $recalcNonMandatoryPool[ array_rand( $recalcNonMandatoryPool ) ];
//        }
//        return $pickedQuestions;
//    }
//
//    public function generateQuestionCache( array $questions ): array
//    {
//        $questionsCache = [];
//        foreach( $questions as $question )
//        {
//            $questionProposals = $question->getProposals();
//            $proposalsCache = [];
//            foreach( $questionProposals as $questionProposal )
//            {
//                $proposalsCache[] = [
//                    'id'                => $questionProposal->getId(),
//                    'wording'           => $questionProposal->getWording(),
//                    'isCorrectAnswer'   => $questionProposal->getIsCorrectAnswer(),
//                ];
//            }
//            $questionsCache[] = [
//                'id'         => $question->getId(),
//                'wording'    => $question->getWording(),
//                'isMultiple' => $question->getIsMultiple(),
//                'difficulty' => $question->getDifficulty(),
//                'proposals'  => $proposalsCache
//            ];
//        }
//        return $questionsCache;
//    }
}
