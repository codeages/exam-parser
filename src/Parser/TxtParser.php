<?php

namespace ExamParser\Parser;

use ExamParser\Parser\FileReader\TxtReader;

class TxtParser extends BaseParser implements ParserInterface
{
    protected $questions = array();

    /**
     * @param $filePath
     * @param array $options
     * @return false|string
     * @throws \ExamParser\Exception\InvalidFileException
     */
    public function read($filePath)
    {
        $reader = new TxtReader($filePath, $this->options);
        return $reader->read();
    }

    public function parser($content)
    {
        $content = $this->filterStartSignal($content);
        $content = $this->filterMaterialSignal($content);
        $questionsArray = $this->resolveContent($content);
        foreach ($questionsArray as $question) {
            $this->matchQuestion($question);
        }

        return $this->questions;
    }
}
