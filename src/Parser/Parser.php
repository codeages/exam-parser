<?php

namespace ExamParser\Parser;

use ExamParser\Parser\QuestionType\QuestionTypeFactory;

class Parser
{
    const START_SINGLE = '【导入开始】';

    const MATERIAL_START_SIGNAL = '【材料题开始】';

    const CODE_MATERIAL_START_SIGNAL = '<#材料题开始#>';

    const MATERIAL_END_SIGNAL = '【材料题结束】';

    const CODE_MATERIAL_END_SIGNAL = '<#材料题结束#>';

    const UNCERTAIN_CHOICE_SIGNAL = '【不定项选择题】';

    protected $type = '';

    protected $body = '';

    protected $testpaperTitle = '';

    protected $testpaperDesc = '';

    protected $questions = array();

    public function __construct($type, $body, $options = array())
    {
        $this->type = $type;
        $this->body = $body;
    }

    public function parser()
    {
        $content = $this->filterStartSignal();
        $content = $this->filterMaterialSignal($content);
        $questionsArray = $this->resolveContent($content);
        $questions = array();
        foreach ($questionsArray as $question) {
            $questions[] = $this->matchQuestion($question);
        }
    }

    protected function filterStartSignal()
    {
        $bodyArray = explode(PHP_EOL.self::START_SINGLE.PHP_EOL, $this->body);
        if (count($bodyArray) == 2) {
            return $bodyArray[1];
        }

        return $this->body;
    }

    protected function filterMaterialSignal($content)
    {
        $pattern = "/".PHP_EOL."【材料题开始】(\S|\s){0,}【材料题结束】".PHP_EOL."/";
        $replacement = "preg_replace('/\\n\\n/', '<#===========#>', $2)";
        $content = preg_replace_callback(
            $pattern,
            function ($matches) {
                $str = str_replace('【材料题开始】', '<#材料题开始#>', $matches[0]);
                $str = str_replace('【材料题结束】', '<#材料题结束#>', $str);
                $str = str_replace(PHP_EOL.PHP_EOL, '<#========#>', $str);
                return $str;
            } ,
            $content);
        return $content;
    }

    protected function resolveContent($content)
    {
        $contentArray = explode(PHP_EOL.PHP_EOL, $content);
        $index = 0;
        foreach ($contentArray as $elem) {
            // if (strpos(trim($elem), self::MATERIAL_START_SIGNAL) === 0) {
            //     $questionArray[$index] = $elem;
            //     $material = true;
            // } else if (!empty($material)) {
            //     $questionArray[$index] .= PHP_EOL.$elem;
            // } else if (strpos(trim($elem), self::MATERIAL_END_SIGNAL) === 0) {
            //     $questionArray[$index] .= PHP_EOL.$elem;
            //     $material = false;
            //     $index++;
            // } else {
            //     $questionArray[$index] = $elem;
            //     $index++;
            // }

            $questionArray[$index] = $elem;
            $index++;
        }
        return $questionArray;
    }

    public function matchQuestion($questionStr)
    {
        $questionStr = trim($questionStr);
        $question = array();
        $lines = explode(PHP_EOL, $questionStr);
        $lines = preg_replace('/^(答案|参考答案|正确答案|\[答案\]|\[参考答案\]|\[正确答案\]|【答案】|【正确答案】|【参考答案】)/','<#答案#>',$lines);
        $lines = preg_replace('/^(难度|\[难度\]|【难度】)/','<#难度#>',$lines);
        $lines = preg_replace('/^(分数|\[分数\]|【分数】)/','<#分数#>',$lines);
        $lines = preg_replace('/^(解析|\[解析\]|【解析】)/','<#解析#>',$lines);
        $lines = preg_replace('/^([A-Z])(\.|\\s)/', '<#$1#>', $lines, -1, $count);
        $lines = preg_replace('/(\(正确\)|（正确）)\s{0,}/', '<#正确#>', $lines);
        $lines = preg_replace('/(\(错误\)|（错误）)\s{0,}/', '<#错误#>', $lines);

        if (strpos(trim($lines[0]), self::CODE_MATERIAL_START_SIGNAL) === 0) {
            $type = 'material';
        } else if (strpos(trim($lines[0]), self::UNCERTAIN_CHOICE_SIGNAL) === 0) {
            $type = 'uncertain_choice';
        }else if ($count == 0) {
            if (preg_match('/\[\[(\S|\s){0,}\]\]/', $lines[0])) {
                $type = 'fill';
            } else if (preg_match('/(\<\#正确\#\>|\<\#错误\#\>)/', $lines[0])) {
                $type = 'determine';
            } else {
                $type = 'essay';
            }
        } else {
            $type = 'choice';
        }

        $questionType = QuestionTypeFactory::create($this->toCamelCase($type));
        $this->questions[] = $questionType->convert($lines);
    }

    // public function getTestpaperTitle()
    // {

    // }

    // public function getTestpaperDesc()
    // {

    // }

    public function getQuestions()
    {
        return $this->questions;
    }

    protected function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/',function($matchs)
        {
            return '_'.strtolower($matchs[0]);
        },$str);
        return trim(preg_replace('/_{2,}/','_',$dstr),'_');
    }

    //下划线命名到驼峰命名
    protected function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len=count($array);
        if($len>1)
        {
            for($i=1;$i<$len;$i++)
            {
                $result.= ucfirst($array[$i]);
            }
        }
        return $result;
    }


}
