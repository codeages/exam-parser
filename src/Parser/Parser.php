<?php

namespace ExamParser\Parser;

class Parser
{
    const START_SINGLE = '【导入开始】';

    const MATERIAL_START_SIGNAL = '【材料题开始】';

    const MATERIAL_END_SIGNAL = '【材料题结束】';

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
        $questionsArray = $this->resolveContent($content);
        $questions = array();
        foreach ($questionsArray as $question) {
            $questions[] = $this->matchQuestion($question);
        }
    }

    protected function filterStartSignal()
    {
        $bodyArray = explode(self::START_SINGLE, $this->body);
        if (count($bodyArray) == 2) {
            return $bodyArray[1];
        }

        return $this->body;
    }

    protected function resolveContent($content)
    {
        $contentArray = explode(PHP_EOL.PHP_EOL, $content);
        $questionArray = array();
        $index = 0;
        foreach ($contentArray as $elem) {
            if (strpos(trim($elem), self::MATERIAL_START_SIGNAL) === 0) {
                $questionArray[$index] = $elem;
                $material = true;
            } else if (!empty($material)) {
                $questionArray[$index] .= PHP_EOL.$elem;
            } else if (strpos(trim($elem), self::MATERIAL_END_SIGNAL) === 0) {
                $questionArray[$index] .= PHP_EOL.$elem;
                $material = false;
                $index++;
            } else {
                $questionArray[$index] = $elem;
                $index++;
            }
        }
        return $questionArray;
    }

    public function matchQuestion($questionStr)
    {
        $lines = explode(PHP_EOL, $questionStr);
        if 

    }

    public function getTestpaperTitle()
    {

    }

    public function getTestpaperDesc()
    {

    }

    public function getQuestions()
    {

    }


}
