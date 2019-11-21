<?php

namespace ExamParser\QuestionType;

use ExamParser\Dumper\DumperInterface;

interface QuestionInterface
{
    public function convert($questionLines);

    public function isMatch($questionLines);

    public function dump($item, DumperInterface $dumper);

    public function replaceSignals(&$content);
}
