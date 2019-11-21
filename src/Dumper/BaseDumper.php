<?php

namespace ExamParser\Dumper;

use ExamParser\QuestionType\QuestionFactory;
use ExamParser\QuestionType\QuestionInterface;

abstract class BaseDumper
{
    protected $filename;

    protected $cachedQuestionTypes = array();

    public function __construct($dumpPath)
    {
        $this->filename = $dumpPath;
    }

    abstract public function dump($questions);
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

}
