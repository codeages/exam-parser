<?php

namespace ExamParser\Parser;

interface ParserInterface
{
    public function parser($content);

    public function read($filePath);

}
