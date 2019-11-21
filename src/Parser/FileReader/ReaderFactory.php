<?php

namespace ExamParser\Parser\FileReader;

use ExamParser\Exception\InvalidFileException;
use ExamParser\Exception\TypeNotFoundException;
use ExamParser\Util\CommonUtil;
use ExamParser\Constants\ParserType;

class ReaderFactory
{
    /**
     * @param $filePath
     * @param $options
     * @return mixed
     * @throws InvalidFileException
     */
    public static function createReader($filePath, $options)
    {
        $pathInfo = pathinfo($filePath);
        if (!in_array($pathInfo['extension'], ParserType::types())) {
            throw new InvalidFileException('Unsupported File Format!');
        }
        $readerType = CommonUtil::toCamelCase($pathInfo['extension']);
        $class = 'ExamParser\\Parser\\FileReader\\'.$readerType.'Reader';

        return new $class($filePath, $options);
    }
}
