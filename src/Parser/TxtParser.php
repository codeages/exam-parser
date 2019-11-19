<?php

namespace ExamParser\Parser;

use ExamParser\Parser\FileReader\TxtReader;

class TxtParser extends BaseParser implements ParserInterface
{
    /**
     * @param $filePath
     * @param array $options
     * @return false|string
     * @throws \ExamParser\Exception\InvalidFileException
     */
    public function read($filePath, $options = array())
    {
        $reader = new TxtReader($filePath, $options);
        return $reader->read();
    }

    public function parser($content)
    {
        return
    }
}
