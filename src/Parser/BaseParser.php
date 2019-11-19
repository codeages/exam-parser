<?php

namespace ExamParser\Parser;

class BaseParser
{
    protected $filePath;

    /**
     * @var array
     * [
     *  'resourceTmpPath' => '/tmp',
     *  'questionTypes' => ['material', 'fill', 'determine', 'essay', 'choice', ],
     * ]
     */
    protected $options;

    public function __construct($filePath, $options = array())
    {
        $this->filePath = $filePath;
        $this->options = $options;
    }
}
