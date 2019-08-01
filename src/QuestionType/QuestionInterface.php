<?php

namespace ExamParser\QuestionType;

interface QuestionInterface
{
    public function convert($questionLines);

    public function isMatch($questionLines);

    public function write($question);
}