<?php

namespace ExamParser\Reader;

use ZipArchive;
use DOMDocument;

class ReadDocx
{
    const DOCUMENT_XML_PATH = 'word/document.xml';

    const DOCUMENT_RELS_XML_PATH = 'word/_rels/document.xml.rels';

    const DOCUMENT_PREFIX = 'word/';
    /**
     * @var string
     */
    protected $docxPath;

    /**
     * @var DOMDocument
     */
    protected $docXml;

    /**
     * @var DOMDocument
     */
    protected $relsXml;

    public function __construct($docxPath)
    {
        $this->docxPath = $docxPath;
        $this->readZip();
    }

    public function getDocxPath()
    {
        return $this->docxPath;
    }

    protected function readZip()
    {
        $path = $this->docxPath;
        $zip = new ZipArchive();
        if (true === $zip->open($path)) {
            if (false !== ($index = $zip->locateName(self::DOCUMENT_XML_PATH))) {
                $xml = $zip->getFromIndex($index);
            }
            $zip->close();
        } else {
            die('non zip file');
        }

        if (true === $zip->open($path)) {
            if (false !== ($index = $zip->locateName(self::DOCUMENT_RELS_XML_PATH))) {
                $xmlRels = $zip->getFromIndex($index);
            }
            $zip->close();
        } else {
            die('non zip file');
        }

        $docXml = new DOMDocument();
        $docXml->encoding = mb_detect_encoding($xml);
        $docXml->preserveWhiteSpace = false; //default true
        $docXml->formatOutput = true; //default true
        $docXml->loadXML($xml);

        $this->docXml = $docXml;

        $relsXml = new DOMDocument();
        $relsXml->encoding = mb_detect_encoding($xmlRels);
        $relsXml->preserveWhiteSpace = false;
        $relsXml->formatOutput = true;
        $relsXml->loadXML($xmlRels);

        $this->relsXml = $relsXml;
    }

    public function convertImage()
    {
        $relsList = $this->relsXml->getElementsByTagName('Relationship');
        $rels = array();
        foreach ($relsList as $relXml) {
            $rels[$relXml->getAttribute('Id')] = $relXml->getAttribute('Target');
        }

        $imagesList = $this->docXml->getElementsByTagName('drawing');

        foreach ($imagesList as $key => $imageXml) {
            $imageId = $imageXml->getElementsByTagName('blip')->item(0)->getAttribute('r:embed');
            if (isset($rels[$imageId])) {
                $file = $this->getZipResource($rels[$imageId]);
                if ($file) {
                    $ext = pathinfo($rels[$imageId], PATHINFO_EXTENSION);
                    $imageXml->textContent = sprintf('<img src="data:image/%s;base64,%s">', $ext, base64_encode($file));
                }
            }
        }
        $this->docXml->saveXML();
        $paragraphList = $this->docXml->getElementsByTagName('p');
        $text = '';
        foreach ($paragraphList as $paragraph) {
            $text .= $paragraph->textContent.PHP_EOL;
        }

        return $text;
    }

    protected function getZipResource($filename)
    {
        $filename = self::DOCUMENT_PREFIX.$filename;
        $path = $this->docxPath;
        $zip = new ZipArchive();
        $file = null;
        if (true === $zip->open($path)) {
            if (false !== ($index = $zip->locateName($filename))) {
                $file = $zip->getFromIndex($index);
            }
            $zip->close();
        } else {
            die('non zip file');
        }

        return $file;
    }
}
