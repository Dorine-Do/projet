<?php

namespace App\Helpers;

use App\Entity\Main\Module;
use App\Entity\Main\Qcm;
use App\Repository\InstructorRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
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

    private function calculByDifficultyCombinations ($chosenDifficultyName, $otherDifficultyName1, $otherDifficultyName2, $totalQuestions, $questionsByDifficultyScore) : array
    {
        $combinations = [];

        $maxQuantityChosenDifficultyScore = end($questionsByDifficultyScore[$chosenDifficultyName]);
        dump($chosenDifficultyName);
        dump($maxQuantityChosenDifficultyScore);
        $otherDifficultyUsableQuestionsQuantity1 = array_filter($questionsByDifficultyScore[$otherDifficultyName1], function ($otherDifficultyQuestionQuantityScore) use ($maxQuantityChosenDifficultyScore) {
            return $otherDifficultyQuestionQuantityScore['score'] < $maxQuantityChosenDifficultyScore;
        });

        $otherDifficultyUsableQuestionsQuantity2 = array_filter($questionsByDifficultyScore[$otherDifficultyName2], function ($otherDifficultyQuestionQuantityScore) use ($maxQuantityChosenDifficultyScore) {
            return $otherDifficultyQuestionQuantityScore['score'] < $maxQuantityChosenDifficultyScore;
        });

        foreach ($questionsByDifficultyScore[$chosenDifficultyName] as $chosenDifficultyUsableQuestionQuantity) {
            foreach ($otherDifficultyUsableQuestionsQuantity1 as $otherDifficultyUsableQuestionQuantity1) {
                foreach ($otherDifficultyUsableQuestionsQuantity2 as $otherDifficultyUsableQuestionQuantity2) {
                    $minimumNonDifficultQuestionsRequired = $totalQuestions - $chosenDifficultyUsableQuestionQuantity['quantity'];
                    if (
                        $otherDifficultyUsableQuestionQuantity1['quantity']
                        +
                        $otherDifficultyUsableQuestionQuantity2['quantity']
                        ===
                        $minimumNonDifficultQuestionsRequired
                        &&
                        $otherDifficultyUsableQuestionQuantity1['score'] < $chosenDifficultyUsableQuestionQuantity['score']
                        &&
                        $otherDifficultyUsableQuestionQuantity2['score'] < $chosenDifficultyUsableQuestionQuantity['score']
                    ) {

                        $combinations[] = [
                            $chosenDifficultyName => $chosenDifficultyUsableQuestionQuantity['quantity'],
                            $otherDifficultyName1 => $otherDifficultyUsableQuestionQuantity1['quantity'],
                            $otherDifficultyName2 => $otherDifficultyUsableQuestionQuantity2['quantity']
                            ];

//                        $combinations[] = [
//                            $chosenDifficultyName =>
//                                [
//                                    'quanty' => $chosenDifficultyUsableQuestionQuantity['quantity'],
//                                    'score' => $chosenDifficultyUsableQuestionQuantity['score']
//                                ],
//                            $otherDifficultyName1 =>
//                                [
//                                    'quanty' => $otherDifficultyUsableQuestionQuantity1['quantity'],
//                                    'score' => $otherDifficultyUsableQuestionQuantity1['score']
//                                ],
//                            $otherDifficultyName2 =>
//                                [
//                                    'quanty' => $otherDifficultyUsableQuestionQuantity2['quantity'],
//                                    'score' => $otherDifficultyUsableQuestionQuantity2['score']
//                                ]
//                        ];
                    }
                }
            }
        }


        return $combinations;
    }

    private function calcQuestionsNumberToPickByDifficulty() : array
    {
        $totalQuestions = $this->_trainingQcmQuestionQuantity;
        if ($this->_type === 'official') {
            $totalQuestions = $this->_officialQcmQuestionQuantity;
        }

        $questionsNbrByDifficulty = [
            'easy' => 0,
            'medium' => 0,
            'difficult' => 0
        ];

        /********************************************************************************/

        $availableEasyQuestions = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => false,
            'isEnabled' => true,
            'module' => $this->_module,
            'difficulty' => 1
        ]);

        $availableMediumQuestions = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => false,
            'isEnabled' => true,
            'module' => $this->_module,
            'difficulty' => 2
        ]);

        $availableDifficultQuestions = $this->_questionRepo->findBy([
            'isMandatory' => false,
            'isOfficial' => false,
            'isEnabled' => true,
            'module' => $this->_module,
            'difficulty' => 3
        ]);

        $availableEasyQuestionQuantity = count($availableEasyQuestions);
        $availableMediumQuestionQuantity = count($availableMediumQuestions);
        $availableDifficultQuestionQuantity = count($availableDifficultQuestions);
        dump($availableEasyQuestionQuantity);
        dump($availableMediumQuestionQuantity);
        dump($availableDifficultQuestionQuantity);
        if (!($availableEasyQuestionQuantity + $availableMediumQuestionQuantity + $availableDifficultQuestionQuantity >= $totalQuestions)) {
            dd('pas assez de questions');
            // pas assez de question pour faire un QCM pour ce module quelle que soit la difficulté voulue
        }

        $questionsByDifficultyScore = [];
        for ($eqq = 0; $eqq < $availableEasyQuestionQuantity; $eqq++) {
            $questionsByDifficultyScore['easy'][] = [
                'quantity' => $eqq,
                'score' => $eqq
            ];
        }
        for ($mqq = 0; $mqq < $availableMediumQuestionQuantity; $mqq++) {
            $questionsByDifficultyScore['medium'][] = [
                'quantity' => $mqq,
                'score' => $mqq * 2
            ];
        }
        for ($dqq = 0; $dqq < $availableDifficultQuestionQuantity; $dqq++) {
            $questionsByDifficultyScore['difficult'][] = [
                'quantity' => $dqq,
                'score' => $dqq * 3
            ];
        }

        $combinations = [];
        /********************************************************************************/

        switch ($this->_difficulty) {
            case 1:

                $combinations = $this->calculByDifficultyCombinations('easy', 'medium', 'difficult', $totalQuestions, $questionsByDifficultyScore);

//                $maxQuantityEasyScore = end($questionsByDifficultyScore['easy']);
//
//                $mediumUsableQuestionsQuantity = array_filter($questionsByDifficultyScore['medium'], function ($mediumQuestionQuantityScore) use ($maxQuantityEasyScore) {
//                    return $mediumQuestionQuantityScore['score'] < $maxQuantityEasyScore;
//                });
//
//                $difficultUsableQuestionsQuantity = array_filter($questionsByDifficultyScore['difficult'], function ($difficultQuestionQuantityScore) use ($maxQuantityEasyScore) {
//                    return $difficultQuestionQuantityScore['score'] < $maxQuantityEasyScore;
//                });
//
//                foreach ($questionsByDifficultyScore['easy'] as $easyUsableQuestionQuantity) {
//                    foreach ($mediumUsableQuestionsQuantity as $mediumUsableQuestionQuantity) {
//                        foreach ($difficultUsableQuestionsQuantity as $difficultUsableQuestionQuantity) {
//                            $minimumNonEasyQuestionsRequired = $totalQuestions - $easyUsableQuestionQuantity['quantity'];
//                            if (
//                                $mediumUsableQuestionQuantity['quantity'] + $difficultUsableQuestionQuantity['quantity'] === $minimumNonEasyQuestionsRequired
//                                && $mediumUsableQuestionQuantity['score'] < $maxQuantityEasyScore['score']
//                                && $difficultUsableQuestionQuantity['score'] < $maxQuantityEasyScore['score']
//                            ) {
//                                $combinations[] = [
//                                    'easy' => $easyUsableQuestionQuantity['quantity'],
//                                    'medium' => $mediumUsableQuestionQuantity['quantity'],
//                                    'difficult' => $difficultUsableQuestionQuantity['quantity']
//                                ];
//                            }
//                        }
//                    }
//                }
//                break;
                break;
            case 2:

                $combinations = $this->calculByDifficultyCombinations('medium', 'easy', 'difficult', $totalQuestions, $questionsByDifficultyScore);

//                $maxQuantityMediumScore = end($questionsByDifficultyScore['medium']);
//
//                $easyUsableQuestionsQuantity = array_filter($questionsByDifficultyScore['easy'], function ($easyQuestionQuantityScore) use ($maxQuantityMediumScore) {
//                    return $easyQuestionQuantityScore['score'] < $maxQuantityMediumScore;
//                });
//
//                $difficultUsableQuestionsQuantity = array_filter($questionsByDifficultyScore['difficult'], function ($difficultQuestionQuantityScore) use ($maxQuantityMediumScore) {
//                    return $difficultQuestionQuantityScore['score'] < $maxQuantityMediumScore;
//                });
//
//                $combinations = [];
//                foreach ($questionsByDifficultyScore['medium'] as $mediumUsableQuestionQuantity) {
//                    foreach ($easyUsableQuestionsQuantity as $easyUsableQuestionQuantity) {
//                        foreach ($difficultUsableQuestionsQuantity as $difficultUsableQuestionQuantity) {
//                            $minimumNonMediumQuestionsRequired = $totalQuestions - $mediumUsableQuestionQuantity['quantity'];
//                            if (
//                                $easyUsableQuestionQuantity['quantity'] + $difficultUsableQuestionQuantity['quantity'] === $minimumNonMediumQuestionsRequired
//                                && $easyUsableQuestionQuantity['score'] < $maxQuantityMediumScore['score']
//                                && $difficultUsableQuestionQuantity['score'] < $maxQuantityMediumScore['score']
//                            ) {
//                                $combinations[] = [
//                                    'easy' => $easyUsableQuestionQuantity['quantity'],
//                                    'medium' => $mediumUsableQuestionQuantity['quantity'],
//                                    'difficult' => $difficultUsableQuestionQuantity['quantity']
//                                ];
//                            }
//                        }
//                    }
//                }
//                break;
                break;
            case 3:

                $combinations = $this->calculByDifficultyCombinations('difficult', 'easy', 'medium', $totalQuestions, $questionsByDifficultyScore);

//                $maxQuantityDifficultScore = end($questionsByDifficultyScore['difficult']);
//
//                $easyUsableQuestionsQuantity = array_filter($questionsByDifficultyScore['easy'], function ($easyQuestionQuantityScore) use ($maxQuantityDifficultScore) {
//                    return $easyQuestionQuantityScore['score'] < $maxQuantityDifficultScore;
//                });
//
//                $mediumUsableQuestionsQuantity = array_filter($questionsByDifficultyScore['medium'], function ($mediumQuestionQuantityScore) use ($maxQuantityDifficultScore) {
//                    return $mediumQuestionQuantityScore['score'] < $maxQuantityDifficultScore;
//                });
//
//                foreach ($questionsByDifficultyScore['difficult'] as $difficultUsableQuestionQuantity) {
//                    foreach ($easyUsableQuestionsQuantity as $easyUsableQuestionQuantity) {
//                        foreach ($mediumUsableQuestionsQuantity as $mediumUsableQuestionQuantity) {
//                            $minimumNonDifficultQuestionsRequired = $totalQuestions - $difficultUsableQuestionQuantity['quantity'];
//                            if (
//                                $easyUsableQuestionQuantity['quantity'] + $mediumUsableQuestionQuantity['quantity'] === $minimumNonDifficultQuestionsRequired
//                                && $easyUsableQuestionQuantity['score'] < $maxQuantityDifficultScore['score']
//                                && $mediumUsableQuestionQuantity['score'] < $maxQuantityDifficultScore['score']
//                            ) {
//                                $combinations[] = [
//                                    'easy' => $easyUsableQuestionQuantity['quantity'],
//                                    'medium' => $mediumUsableQuestionQuantity['quantity'],
//                                    'difficult' => $difficultUsableQuestionQuantity['quantity']
//                                ];
//                            }
//                        }
//                    }
//                }
                break;
        }

        if (count($combinations) === 0) {
            dd('Pas assez de combinaisons');
            // Pas assez de questions pour faire un QCM moyen
        } else {
            $questionsNbrByDifficulty = $combinations[array_rand($combinations)];
        }
        dd($questionsNbrByDifficulty);
        return $questionsNbrByDifficulty;
    }



}