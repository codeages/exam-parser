<?php

namespace ExamParser\Parser\QuestionType;

interface QuestionInterface
{
    public function convert($questionLines);

    public function isMatch($questionLines);
}