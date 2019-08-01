<?php

namespace ExamParser\Parser\ParserType;

interface ParserInterface
{
    public function parser($source);

    public function readSource($filePath);

}