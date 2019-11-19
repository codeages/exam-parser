<?php

namespace ExamParser\Parser\FileReader;

use ExamParser\Exception\InvalidFileException;

class TxtReader implements ReaderInterface
{
    /**
     * @var
     * 文件路径
     */
    protected $filePath;

    public function __construct($filePath, $options = array())
    {
        $this->filePath = $filePath;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return false|string
     * @throws InvalidFileException
     */
    public function read()
    {
        if (!is_file($this->filePath)) {
            throw new InvalidFileException('Invalid File!');
        }

        return file_get_contents($this->filePath);

    }
}
