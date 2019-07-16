<?php

namespace ExamParser\Parser\QuestionType;

abstract class AbstractQuestion
{
    const ANSWER_SIGNAL = '<#答案#>';

    const DIFFICULTY_SIGNAL = '<#难度#>';

    const SCORE_SIGNAL = '<#分数#>';

    const ANALYSIS_SIGNAL = '<#解析#>';

    const DEFAULT_SCORE = 2.0;

    const DEFAULT_DIFFICULTY = 'normal';

    abstract public function convert($questionLines);

    protected function matchDifficulty(&$question, $line)
    {
        if (0 === strpos(trim($line), self::DIFFICULTY_SIGNAL)) {
            $difficulty = str_replace(self::DIFFICULTY_SIGNAL, '', $line);
            $difficultyCode = 'normal';
            if ('简单' == trim($difficulty)) {
                $difficultyCode = 'simple';
            }

            if ('一般' == trim($difficulty)) {
                $difficultyCode = 'normal';
            }

            if ('困难' == trim($difficulty)) {
                $difficultyCode = 'difficulty';
            }
            $question['difficulty'] = $difficultyCode ?: self::DEFAULT_DIFFICULTY;

            return true;
        }

        return false;
    }

    protected function matchScore(&$question, $line)
    {
        if (0 === strpos(trim($line), self::SCORE_SIGNAL)) {
            preg_match('/(([1-9]\d*\.\d*|0\.\d*[1-9]\d*)|[1-9]\d*)/', $line, $matches);
            $question['score'] = isset($matches[0]) ? $matches[0] : self::DEFAULT_SCORE;

            return true;
        }

        return false;
    }

    protected function matchAnalysis(&$question, $line)
    {
        if (0 === strpos(trim($line), self::ANALYSIS_SIGNAL)) {
            $analysis = str_replace(self::ANALYSIS_SIGNAL, '', $line);
            $question['analysis'] = $analysis;

            return true;
        }

        return false;
    }
}
