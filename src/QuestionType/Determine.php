<?php

namespace ExamParser\QuestionType;

use ExamParser\Constants\QuestionElement;
use ExamParser\Constants\QuestionErrors;
use ExamParser\Dumper\DumperInterface;

class Determine extends AbstractQuestion implements QuestionInterface
{
    const ANSWER_RIGHT_SIGNAL = '<#正确#>';

    const ANSWER_WRONG_SIGNAL = '<#错误#>';

    public function convert($questionLines)
    {
        $question = array(
            'type' => 'determine',
            'stem' => '',
            'difficulty' => 'normal',
            'score' => 2.0,
            'analysis' => '',
            'answer' => null,
        );
        $preNode = QuestionElement::STEM;
        foreach ($questionLines as $line) {
            //处理答案
            if ($this->matchAnswer($question, $line, $preNode)) {
                continue;
            }
            //处理难度
            if ($this->matchDifficulty($question, $line, $preNode)) {
                continue;
            }
            //处理分数
            if ($this->matchScore($question, $line, $preNode)) {
                continue;
            }

            //处理解析
            if ($this->matchAnalysis($question, $line, $preNode)) {
                continue;
            }

            //处理题干
            if ($this->matchStem($question, $line, $preNode)) {
                continue;
            }
        }

        $this->checkErrors($question);

        return $question;
    }

    public function isMatch($questionLines)
    {
        preg_match('/(\<\#正确\#\>|\<\#错误\#\>)/', trim(implode('', $questionLines)));
    }

    public function dump($item, DumperInterface $dumper)
    {
        if ('determine' != $item['type']) {
            return;
        }

        $dumper->buildStem($item['stem'], $item['num'], $item['answer']);
        $this->dumpCommonModule($item, $dumper);
    }

    protected function matchAnswer(&$question, $line, &$preNode)
    {
        $pattern = '/('.self::ANSWER_RIGHT_SIGNAL.'|'.self::ANSWER_WRONG_SIGNAL.')/';
        if (preg_match($pattern, $line, $matches)) {
            if (self::ANSWER_RIGHT_SIGNAL == $matches[0]) {
                $question['answer'] = true;
            }
            if (self::ANSWER_WRONG_SIGNAL == $matches[0]) {
                $question['answer'] = false;
            }

            $stemStr = str_replace(self::ANSWER_RIGHT_SIGNAL, '', $line);
            $stemStr = str_replace(self::ANSWER_WRONG_SIGNAL, '', $stemStr);
            $question['stem'] .= preg_replace('/^((\d{0,5}(\.|、|。|\s))|((\(|（)\d{0,5}(\)|）)))/', '', $stemStr);
            $preNode = QuestionElement::ANSWER;

            return true;
        }

        return false;
    }

    protected function checkErrors(&$question)
    {
        //判断题干是否有错
        if (empty($question[QuestionElement::STEM])) {
            $question['errors'][QuestionElement::STEM] = $this->getError(QuestionElement::STEM, QuestionErrors::NO_STEM);
        }
    }
}
