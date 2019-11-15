<?php

namespace ExamParser\Parser;

use ExamParser\Exception\TypeNotFoundException;
use ExamParser\Util\CommonUtil;
use ExamParser\Constants\ParserType;

class ParserFactory
{
    /**
     * Create New Parser
     * @param $type
     * @return mixed
     * @throws TypeNotFoundException
     */
    public static function createParser($type)
    {
        if (!in_array($type, ParserType::types())) {
            throw new TypeNotFoundException("{$type} is not a valid type");
        }
        $parserType = CommonUtil::toCamelCase($type);
        $class = 'ExamParser\\Parser\\'.$parserType.'Parser';

        return new $class();
    }
}
