<?php

namespace ExamParser\Parser;

use ExamParser\Constants\ParserSignal;
use ExamParser\Exception\InvalidFileException;
use ExamParser\Parser\FileReader\ReaderFactory;
use ExamParser\Parser\FileReader\ReaderInterface;
use ExamParser\QuestionType\QuestionFactory;
use ExamParser\QuestionType\QuestionInterface;

class Parser implements ParserInterface
{
    protected $questions = array();

    /**
     * @var array
     * [
     *  'resourceTmpPath' => '/tmp',
     *  'questionTypes' => ['material', 'fill', 'determine', 'essay', 'choice'],
     * ]
     */
    protected $options = array(
        'resourceTmpPath' => '/tmp',
        'questionTypes' => array('material', 'choice', 'fill', 'determine', 'essay')
    );

    protected $cachedQuestionTypes = array();

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param $filePath
     * @return mixed
     * @throws InvalidFileException
     * @throws \ExamParser\Exception\TypeNotFoundException
     */
    public function parse($filePath)
    {
        $content = $this->createReader($filePath)->read();
        $content = $this->filterStartSignal($content);
        $content = $this->convertQuestionsSignal($content);
        $questionsArray = $this->resolveContent($content);
        foreach ($questionsArray as $question) {
            $this->matchQuestion($question);
        }
        return $this->questions;
    }

    protected function convertQuestionsSignal($content)
    {
        foreach ($this->options['questionTypes'] as $type) {
            $this->getQuestionType($type)->replaceSignals($content);
        }
        return $content;
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
    protected function matchQuestion($questionStr)
    {
        $questionStr = trim($questionStr);
        $lines = explode(PHP_EOL, $questionStr);
        $lines = $this->replaceSignals($lines);

        foreach ($this->options['questionTypes'] as $type) {
            if ($this->getQuestionType($type)->isMatch($lines)) {
                $this->questions[] = $this->getQuestionType($type)->convert($lines);
                break;
            }
        }
    }

    protected function replaceSignals($lines)
    {
        $lines = preg_replace('/^(答案|参考答案|正确答案|\[答案\]|\[参考答案\]|\[正确答案\]|【答案】|【正确答案】|【参考答案】)(：|:|)/', '<#答案#>', $lines);
        $lines = preg_replace('/^(难度|\[难度\]|【难度】)/', '<#难度#>', $lines);
        $lines = preg_replace('/^(分数|\[分数\]|【分数】)/', '<#分数#>', $lines);
        $lines = preg_replace('/^(解析|\[解析\]|【解析】)/', '<#解析#>', $lines);
        $lines = preg_replace('/^([A-J])(\.|、|。|\\s)/', '<#$1#>', $lines, -1, $count);
        $lines = preg_replace('/(\(正确\)|（正确）)\s{0,}/', '<#正确#>', $lines);
        $lines = preg_replace('/(\(错误\)|（错误）)\s{0,}/', '<#错误#>', $lines);

        return $lines;
    }

    /**
     * @param $type
     * @return QuestionInterface
     */
    protected function getQuestionType($type)
    {
        if (isset($this->cachedQuestionTypes[$type])) {
            return $this->cachedQuestionTypes[$type];
        }

        return $this->cachedQuestionTypes[$type] = QuestionFactory::create($type);
    }

    /**
     * @param $filePath
     * @return ReaderInterface
     * @throws InvalidFileException
     */
    private function createReader($filePath)
    {
        return ReaderFactory::createReader($filePath, $this->options);
    }
}
