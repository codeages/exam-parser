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
            if (preg_match('/<#([A-Z])#>/', $line, $matches)) {
                $question['options'][ord($matches[1])-65] = preg_replace('/<#([A-Z])#>/', '', $line);
                $stemStatus = true;
                continue;
            }
            //处理答案
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
                continue;
            }
            //处理难度
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
                continue;
            }
            //处理分数
            if (strpos(trim($line), self::SCORE_SIGNAL) === 0) {
                preg_match('/(([1-9]\d*\.\d*|0\.\d*[1-9]\d*)|[1-9]\d*)/', $line, $matches);
                $question['score'] = isset($matches[0]) ? $matches[0] : self::DEFAULT_SCORE;
                continue;
            };

            if (!$stemStatus) {
                $question['stem'] .= PHP_EOL.$line;
            }

        }
        return $question;
    }
    
}
