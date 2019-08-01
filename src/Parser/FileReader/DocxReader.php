<?php

namespace ExamParser\Parser\FileReader;

class DocxReader implements ReaderInterface
{
    protected $filePath;

    protected $documentText;

    public function read($filePath, $options)
    {
        $this->filePath = $filePath;
        $this->readZip();
        $this->loadXml();
        $this->applyOptions($options);

        return $this->documentText;
    }

    protected function readZip()
    {

    }

    protected function loadXml()
    {

    }

    protected function applyOptions($options)
    {

    }

    protected function convertImage()
    {

    }
}