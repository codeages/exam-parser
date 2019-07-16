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
        if (strpos(trim($line), self::DIFFICULTY_SIGNAL) === 0) {
            $difficulty = str_replace(self::DIFFICULTY_SIGNAL, '', $line);
            $difficultyCode = 'normal';
            if (trim($difficulty) == '简单') {
                $difficultyCode = 'simple';
            }

            if (trim($difficulty) == '一般') {
                $difficultyCode = 'normal';
            }

            if (trim($difficulty) == '困难') {
                $difficultyCode = 'difficulty';
            }
            $question['difficulty'] = $difficultyCode ? : self::DEFAULT_DIFFICULTY;
            return true;
        }

        return false;
    }

    protected function matchScore(&$question, $line)
    {
        if (strpos(trim($line), self::SCORE_SIGNAL) === 0) {
            preg_match('/(([1-9]\d*\.\d*|0\.\d*[1-9]\d*)|[1-9]\d*)/', $line, $matches);
            $question['score'] = isset($matches[0]) ? $matches[0] : self::DEFAULT_SCORE;
            return true;
        };

        return false;
    }
}
