<?php

namespace ExamParser\Parser;

use ExamParser\Constants\ParserSignal;
use ExamParser\QuestionType\QuestionFactory;

class BaseParser
{
    protected $filePath;

    /**
     * @var array
     * [
     *  'resourceTmpPath' => '/tmp',
     *  'questionTypes' => ['material', 'fill', 'determine', 'essay', 'choice'],
     * ]
     */
    protected $options = array(
        'resourceTmpPath' => '/tmp',
        'questionTypes' => array('material', 'fill', 'determine', 'essay', 'choice')
    );

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param $content
     * @return string
     * 去掉文档说明
     */
    protected function filterStartSignal($content)
    {
        $bodyArray = explode(PHP_EOL.ParserSignal::START_SIGNAL.PHP_EOL, $content);
        if (2 == count($bodyArray)) {
            return $bodyArray[1];
        }

        return $content;
    }

    /**
     * @param $content
     * @return string|string[]|null
     * 处理材料题题组
     */
    protected function filterMaterialSignal($content)
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
            $content);

        return $content;
    }

    /**
     * @param $content
     * @return array
     * 分割字符串为题目字符串数组
     */
    protected function resolveContent($content)
    {
        $pattern = '/'.PHP_EOL.'{2,}/';
        $contentArray = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY);
        $index = 0;
        $questionArray = array();
        foreach ($contentArray as $elem) {
            $questionArray[$index] = $elem;
            ++$index;
        }

        return $questionArray;
    }

    /**
     * @param $questionStr
     * 根据题目字符串转化成题目
     */
    public function matchQuestion($questionStr)
    {
        $questionStr = trim($questionStr);
        $lines = explode(PHP_EOL, $questionStr);
        $lines = preg_replace('/^(答案|参考答案|正确答案|\[答案\]|\[参考答案\]|\[正确答案\]|【答案】|【正确答案】|【参考答案】)(：|:|)/', '<#答案#>', $lines);
        $lines = preg_replace('/^(难度|\[难度\]|【难度】)/', '<#难度#>', $lines);
        $lines = preg_replace('/^(分数|\[分数\]|【分数】)/', '<#分数#>', $lines);
        $lines = preg_replace('/^(解析|\[解析\]|【解析】)/', '<#解析#>', $lines);
        $lines = preg_replace('/^([A-J])(\.|、|。|\\s)/', '<#$1#>', $lines, -1, $count);
        $lines = preg_replace('/(\(正确\)|（正确）)\s{0,}/', '<#正确#>', $lines);
        $lines = preg_replace('/(\(错误\)|（错误）)\s{0,}/', '<#错误#>', $lines);
        $lines = preg_replace('/【不定项选择题】/', '<#不定项选择题#>', $lines);

        if (0 === strpos(trim($lines[0]), ParserSignal::CODE_MATERIAL_START_SIGNAL)) {
            $type = 'material';
        } elseif (0 == $count) {
            if (preg_match('/\[\[(\S|\s)*?\]\]/', $lines[0])) {
                $type = 'fill';
            } elseif (preg_match('/(\<\#正确\#\>|\<\#错误\#\>)/', trim(implode('', $lines)))) {
                $type = 'determine';
            } else {
                $type = 'essay';
            }
        } else {
            $type = 'choice';
        }

        $questionType = QuestionFactory::create($type);
        $this->questions[] = $questionType->convert($lines);
    }
}
