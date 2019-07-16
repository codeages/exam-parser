<?php

namespace ExamParser\Parser\QuestionType;

class Choice extends AbstractQuestion
{
    public function convert($questionLines)
    {
        $stemStatus = false;
        $question = array(
            'stem' => '',
        );
        $answers = array();
        foreach ($questionLines as $line) {
            //处理选项
            if ($this->matchOptions($question, $line)) {
                $stemStatus = true;
                continue;
            }
            //处理答案
            if ($this->matchAnswers($question, $line)) {
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
            };

            if (!$stemStatus) {
                $question['stem'] .= preg_replace('/^\d{0,5}(\.|、|。|\s)/','',$line).PHP_EOL;
            }

        }
        return $question;
    }

    protected function matchOptions(&$question, $line)
    {
        if (preg_match('/<#([A-Z])#>/', $line, $matches)) {
            $question['options'][ord($matches[1])-65] = preg_replace('/<#([A-Z])#>/', '', $line);
            return true;
        }
        return false;
    }

    protected function matchAnswers(&$question, $line)
    {
        if (strpos(trim($line), self::ANSWER_SIGNAL) === 0) {
            preg_match_all('/[A-Z]/', $line, $matches);
            if ($matches) {
                foreach($matches[0] as $answer) {
                    $answers[] = ord($answer)-65;
                }
            }
            $question['answers'] = $answers;
            if (count($answers) > 1) {
                $question['type'] = 'choice';
            }else {
                $question['type'] = 'single_choice';
            }
            return true;
        }
        return false;
    }

    
}
