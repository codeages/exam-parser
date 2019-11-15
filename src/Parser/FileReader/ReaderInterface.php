<?php

namespace ExamParser\Parser\FileReader;

interface ReaderInterface
{
    public function read();

    public function getFilePath();
}
