<?php

namespace ExamParser\Parser\FileReader;

interface ReaderInterface
{
    public function __construct($filePath, $options = array());

    public function read();

    public function getFilePath();
}
