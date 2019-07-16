<?php

namespace ExamParser\Parser\QuestionType;

class Determine extends AbstractQuestion
{
    const ANSWER_RIGHT_SIGNAL = '<#正确#>';

    const ANSWER_WRONG_SIGNAL = '<#错误#>';

    public function convert($questionLines)
    {
        $stemStatus = false;
        $question = array(
            'type' => 'determine',
            'stem' => '',
            'difficulty' => 'normal',
            'score' => 2.0,
            'analysis' => '',
            'answer' => null,
        );
        $answers = array();
        foreach ($questionLines as $line) {
            //处理答案
            if ($this->matchAnswer($question, $line)) {
                $stemStatus = true;
                continue;
            }
            //处理难度
            if ($this->matchDifficulty($question, $line)) {
                $stemStatus = true;
                continue;
            }
            //处理分数
            if ($this->matchScore($question, $line)) {
                $stemStatus = true;
                continue;
            }

            //处理解析
            if ($this->matchAnalysis($question, $line)) {
                $stemStatus = true;
                continue;
            }

            if (!$stemStatus) {
                $question['stem'] .= preg_replace('/^\d{0,5}(\.|、|。|\s)/', '', $line).PHP_EOL;
            }
        }

        return $question;
    }

    protected function matchAnswer(&$question, $line)
    {
        $pattern = '/('.self::ANSWER_RIGHT_SIGNAL.'|'.self::ANSWER_WRONG_SIGNAL.')/';
        if (preg_match($pattern, $line, $matches)) {
            if (self::ANSWER_RIGHT_SIGNAL == $matches[0]) {
                $question['answer'] = true;
            }
            if (self::ANSWER_WRONG_SIGNAL == $matches[0]) {
                $question['answer'] = false;
            }

            return true;
        }

        return false;
    }
}
