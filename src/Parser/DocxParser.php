<?php

namespace ExamParser\Parser;

use ExamParser\Constants\ParserSignal;
use ExamParser\Parser\FileReader\DocxReader;
use ExamParser\QuestionType\QuestionFactory;

class DocxParser extends BaseParser implements ParserInterface
{
    protected $questions = array();

    /**
     * @param $filePath
     * @return string
     * @throws \ExamParser\Exception\ExamException
     */
    public function read($filePath)
    {
        $reader = new DocxReader($filePath, $this->options);
        return $reader->read();
    }

}
