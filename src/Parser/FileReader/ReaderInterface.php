<?php

namespace ExamParser\Parser\FileReader;

interface ReaderInterface
{
    public function read($filePath, $options);
}