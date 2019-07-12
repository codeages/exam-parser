<?php

namespace ExamParser\Parser\QuestionType;

abstract class AbstractQuestion
{
    abstract public function isMatch();

    abstract public function convert();
}