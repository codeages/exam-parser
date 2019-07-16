<?php

namespace ExamParser\Parser\QuestionType;

class Essay extends AbstractQuestion
{
    public function convert($questionLines)
    {
        $stemStatus = false;
        $question = array(
            'type' => 'essay',
            'stem' => '',
            'difficulty' => 'normal',
            'score' => 2.0,
            'analysis' => '',
            'answer' => '',
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

    protected function matchAnswer($question, $line)
    {
        if (0 === strpos(trim($line), self::ANSWER_SIGNAL)) {
            $answer = str_replace(self::ANSWER_SIGNAL, '', $line);
            $question['answer'] = $answer;

            return true;
        }

        return false;
    }
}
