<?php

namespace ExamParser\Parser;

use ExamParser\Parser\Util\CommonUtil;

class Parser
{
    public function createParser($type)
    {
        $parserType = CommonUtil::toCamelCase($type);
        $class = '\\ExamParser\\Parser\\ParserType\\'.$parserType.'Parser';

        return new $class();
    }
}