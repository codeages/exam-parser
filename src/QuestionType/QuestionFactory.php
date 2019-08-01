<?php

namespace ExamParser\QuestionType;

use ExamParser\Util\CommonUtil;

class QuestionFactory
{
    public static function create($type)
    {
        $questionType = CommonUtil::toCamelCase($type);
        $class = '\\ExamParser\\Parser\\QuestionType\\'.$questionType;

        return new $class();
    }
}