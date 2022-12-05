<?php

namespace App\Helpers;

class QcmResultHelper
{
    public static function calcQcmPonderatedScore( $qcm, $studentResponses ) : array
    {
        $questionsCache = $qcm->getQuestionsCache();
        $qcmDifficulty = $qcm->getDifficulty();

        $countIsCorrectAnswer = 0;

        $cumulatedScore = 0;
        $cumulatedPonderations = 0;

        foreach ( $questionsCache as $questionCacheKey => $questionCache )
        {
            $questionDifficulty = $questionCache[$questionCacheKey]['difficulty'];

            $questionPonderation = $questionCache[$questionCacheKey]['difficulty'] === $qcmDifficulty ? 2 : 1;
            $cumulatedPonderations += $questionPonderation;

            foreach ( $studentResponses as $studentAnswerKey => $studentAnswerValue )
            {
                if( $questionsCache[$questionCacheKey]['id'] == $studentAnswerKey )
                {
                    // Radio
                    if ( !$questionsCache[$questionCacheKey]['isMultiple'] )
                    {
                        $radioIsCorrect = 0;
                        $studentAnswerValue = intval($studentAnswerValue);
                        foreach ($questionsCache[$questionCacheKey]['proposals'] as $proposalKey => $proposal)
                        {
                            //Si case cochée par l'etudiant et bonne réponse
                            if(
                                $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isCorrectAnswer']
                                &&
                                $studentAnswerValue === $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id']
                            )
                            {
                                $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 1;
                                $radioIsCorrect ++ ;
                            }
                            // Si case cochée par l'etudiant
                            elseif(
                                !$questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isCorrectAnswer']
                                &&
                                $studentAnswerValue === $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id']
                            )
                            {
                                $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 1;
                            }
                            // Si pas case cochée par l'etudiant
                            else
                            {
                                $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isStudentAnswer'] = 0;
                            }
                        }
                        // Si l'étudiant a répondu juste
                        if ($radioIsCorrect !== 0)
                        {
                            $countIsCorrectAnswer++;
                            $questionsCache[$questionCacheKey]['student_answer_correct'] = 1;
                            $cumulatedScore += 1 * $questionPonderation;
                        }
                        else
                        {
                            $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
                        }
                    } // CheckBox
                    else
                    {
                        $dbAnswersCheck = [
                            'good' => [],
                            'bad' => []
                        ];
                        foreach( $questionsCache[$questionCacheKey]['proposals'] as $proposalKey => $proposal )
                        {
                            if( $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['isCorrectAnswer'] )
                            {
                                $dbAnswersCheck['good'][] = $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id'];
                            }
                            else
                            {
                                $dbAnswersCheck['bad'][] = $questionsCache[$questionCacheKey]['proposals'][$proposalKey]['id'];
                            }
                        }
                        $goodAnswersCount = 0;
                        $badAnswersCount = 0;
                        foreach ($studentAnswerValue as $studentAnswer)
                        {
                            if( in_array( $studentAnswer, $dbAnswersCheck['good'] ) )
                            {
                                $goodAnswersCount++;

                            }
                            elseif( in_array($studentAnswer, $dbAnswersCheck['bad']) )
                            {
                                $badAnswersCount++;

                            }
                            else{
                                $badAnswersCount++;

                            }
                        }

                        // Pour savoir quelles réponses à coché l'étudiant
                        foreach ($questionsCache[$questionCacheKey]['proposals'] as $answerDbKey => $answerDbValue)
                        {
                            if( in_array($answerDbValue['id'], $studentAnswerValue) )
                            {
                                $questionsCache[$questionCacheKey]['proposals'][$answerDbKey]['isStudentAnswer'] = 1;
                            }
                            else
                            {
                                $questionsCache[$questionCacheKey]['proposals'][$answerDbKey]['isStudentAnswer'] = 0;
                            }
                        }

                        if( $goodAnswersCount === count($dbAnswersCheck['good']) && $badAnswersCount === 0 )
                        {
                            $countIsCorrectAnswer ++;
                            $questionsCache[$questionCacheKey]['student_answer_correct'] = 1;
                            $cumulatedScore += 1 * $questionPonderation;
                        }else{
                            $questionsCache[$questionCacheKey]['student_answer_correct'] = 0;
                        }
                    }
                }
            }
        }

//        $nbQuestions = count($questionsCache);
//        $totalScore = (100/$nbQuestions)*$countIsCorrectAnswer;

        $totalScore = 100 * $cumulatedScore / $cumulatedPonderations;

        return [
            'totalScore' => $totalScore,
            'questionsCache' => $questionsCache
        ];
    }
}