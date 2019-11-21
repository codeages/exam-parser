<?php

namespace ExamParser\QuestionType;

use ExamParser\Constants\ParserSignal;
use ExamParser\Constants\QuestionElement;
use ExamParser\Constants\QuestionErrors;
use ExamParser\Dumper\DumperInterface;

class Material extends AbstractQuestion implements QuestionInterface
{
    protected $subQuestions = array();

    public function convert($questionLines)
    {
        $question = array(
            'stem' => '',
            'type' => 'material',
            'score' => 2.0,
            'analysis' => '',
            'difficulty' => 'normal',
            'subQuestions' => array(),
        );

        $preNode = QuestionElement::STEM;

        $question['subQuestions'] = $this->filterSubQuestions($questionLines);

        foreach ($questionLines as $line) {
            if (ParserSignal::CODE_MATERIAL_START_SIGNAL == trim($line) || ParserSignal::CODE_MATERIAL_END_SIGNAL == trim($line)) {
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

    public function dump($item, DumperInterface $dumper)
    {
        if ('material' != $item['type']) {
            return;
        }

        $dumper->writeTag('【材料题开始】'.PHP_EOL);
        $dumper->buildStem($item['stem'], $item['num']);
        $this->dumpCommonModule($item, $dumper);
        $dumper->addTextBreak();
        $dumper->dump($item['subs']);
        $dumper->writeTag('【材料题结束】'.PHP_EOL);
    }

    public function isMatch($questionLines)
    {
        return (0 === strpos(trim($questionLines[0]), ParserSignal::CODE_MATERIAL_START_SIGNAL));
    }

    public function replaceSignals(&$content)
    {
        $pattern = '/'.PHP_EOL."{0,1}【材料题开始】[\s\S]*?【材料题结束】".PHP_EOL.'/';
        $content = preg_replace_callback(
            $pattern,
            function ($matches) {
                $str = preg_replace('/【材料题开始】\s*/', '<#材料题开始#>'.PHP_EOL, $matches[0]);
                $str = preg_replace('/\s*【材料题结束】/', PHP_EOL.'<#材料题结束#>', $str);
                $pattern = '/'.PHP_EOL.'{2,}/';
                $str = preg_replace($pattern, PHP_EOL.'<#材料题子题#>', $str);

                return $str;
            },
            $content
        );
    }

    protected function filterSubQuestions(&$questionLines)
    {
        $subQuestions = array();
        $questionSeq = -1;
        foreach ($questionLines as $key => $line) {
            if (ParserSignal::CODE_MATERIAL_START_SIGNAL == trim($line) || ParserSignal::CODE_MATERIAL_END_SIGNAL == trim($line)) {
                continue;
            }
            if (0 === strpos(trim($line), ParserSignal::CODE_MATERIAL_SUB_QUESTION_START)) {
                ++$questionSeq;
            }

            if ($questionSeq >= 0) {
                $subQuestions[$questionSeq][] = str_replace(ParserSignal::CODE_MATERIAL_SUB_QUESTION_START, '', trim($line));
                unset($questionLines[$key]);
            }
        }
        foreach ($subQuestions as $lines) {
            $count = preg_match_all('/\<\#[A-J]\#\>/', implode(PHP_EOL, $lines), $matches);
            if (0 === strpos(trim($lines[0]), ParserSignal::CODE_MATERIAL_START_SIGNAL)) {
                $type = 'material';
            } elseif (0 == $count) {
                if (preg_match('/\[\[(\S|\s).*?\]\]/', $lines[0])) {
                    $type = 'fill';
                } elseif (preg_match('/(\<\#正确\#\>|\<\#错误\#\>)/', trim(implode('', $lines)))) {
                    $type = 'determine';
                } else {
                    $type = 'essay';
                }
            } else {
                $type = 'choice';
            }

            $questionType = QuestionFactory::create($this->toCamelCase($type));
            $this->subQuestions[] = $questionType->convert($lines);
        }

        return $this->subQuestions;
    }

    //下划线命名到驼峰命名
    protected function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = '';
        $len = count($array);
        for ($i = 0; $i < $len; ++$i) {
            $result .= ucfirst($array[$i]);
        }

        return $result;
    }

    protected function checkErrors(&$question)
    {
        //判断题干是否有错
        if (empty($question[QuestionElement::STEM])) {
            $question['errors'][QuestionElement::STEM] = $this->getError(QuestionElement::STEM, QuestionErrors::NO_STEM);
        }

        //判断是否有子题;无子题不报错
        if (empty($question['subQuestions'])) {
//            $question['errors'][QuestionElement::SUB_QUESTIONS] = $this->getError(QuestionElement::SUB_QUESTIONS, QuestionErrors::NO_SUB_QUESTIONS);
            return;
        }

        //子题是否有错
        foreach ($question['subQuestions'] as $subQuestion) {
            if (!empty($subQuestion['errors'])) {
                $question['errors']['hasSubError'] = true;
            }
        }
    }
}
