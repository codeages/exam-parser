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
}
