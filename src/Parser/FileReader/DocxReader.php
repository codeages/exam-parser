<?php

namespace ExamParser\Parser\FileReader;

use ExamParser\Exception\ExamException;
use ZipArchive;
use DOMDocument;
use Rhumsaa\Uuid\Uuid;

class DocxReader implements ReaderInterface
{
    /**
     * Word2007+ 文档中正文的XML文件相对路径地址
     */
    const DOCUMENT_XML_PATH = 'word/document.xml';

    /**
     * Word2007+ 文档中正文资源文件的XML文件相对路径地址
     */
    const DOCUMENT_RELS_XML_PATH = 'word/_rels/document.xml.rels';

    /**
     * Word2007+ 文件主目录
     */
    const DOCUMENT_PREFIX = 'word/';

    /**
     * Word中厘米和EMU的换算比例
     */
    const CM_EMU = 360000;

    /**
     * 电脑屏幕72ppi，厘米和像素的换算规则
     */
    const CM_PX = 25;

    /**
     * @var mixed|string
     * 临时目录，默认放到/tmp
     */
    protected $resourceTmpPath = '/tmp';

    /**
     * @var
     * 文档路径
     */
    protected $filePath;

    /**
     * @var
     * 文档内容
     */
    protected $documentText = '';

    /**
     * @var DOMDocument
     *                  文档主体xml
     */
    protected $docXml;

    /**
     * @var DOMDocument
     *                  文档资源xml
     */
    protected $relsXml;

    public function __construct($filePath, $options = array())
    {
        $this->filePath = $filePath;
        if (isset($options['resourceTmpPath'])) {
            $this->resourceTmpPath = $options['resourceTmpPath'];
        }
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     * @throws ExamException
     */
    public function read()
    {
        $this->readZip();
        $this->convertImages();

        return $this->documentText;
    }

    /**
     * @throws ExamException
     * 解析Zip文件，抽离出文档和资源xml对象列表
     */
    protected function readZip()
    {
        $this->docXml = $this->loadXml(self::DOCUMENT_XML_PATH);
        $this->relsXml = $this->loadXml(self::DOCUMENT_RELS_XML_PATH);
    }

    /**
     * @param $xmlPath
     * @return DOMDocument
     * @throws ExamException
     * 实例化文档中所给路径的XML文本为DOM对象
     */
    protected function loadXml($xmlPath)
    {
        $path = $this->getFilePath();
        $zip = new ZipArchive();
        if (true === $zip->open($path)) {
            if (false !== ($index = $zip->locateName($xmlPath))) {
                $xml = $zip->getFromIndex($index);
            }
            $zip->close();
            $docXml = new DOMDocument();
            $docXml->encoding = mb_detect_encoding($xml);
            $docXml->preserveWhiteSpace = false; //default true
            $docXml->formatOutput = true; //default true
            $docXml->loadXML($xml);

            return $docXml;
        } else {
            throw new ExamException('file format is invalid');
        }
    }

    /**
     * @throws ExamException
     * 处理文档中的图片
     */
    protected function convertImages()
    {
        $imagesList = $this->docXml->getElementsByTagName('drawing');

        foreach ($imagesList as $key => $imageXml) {
            $this->handleImage($imageXml);
        }
        $this->docXml->saveXML();
        $paragraphList = $this->docXml->getElementsByTagName('p');
        $text = '';
        foreach ($paragraphList as $paragraph) {
            $text .= $paragraph->textContent.PHP_EOL;
        }

        $this->documentText = $text;
    }

    /**
     * @param $imageXml \DOMNode
     * @throws ExamException
     * 将单个图片转化为<img>
     */
    protected function handleImage(&$imageXml)
    {
        /**
         * @var $img \DOMNode
         */
        $img = $imageXml->getElementsByTagName('blip')->item(0);
        if (empty($img)) {
            return;
        }
        $imageId = $img->getAttribute('r:embed');
        /**
         * @var $imageExtend \DOMNode
         */
        $imageExtend = $imageXml->getElementsByTagName('extent')->item(0);
        $cx = (int) ($imageExtend->getAttribute('cx') / self::CM_EMU * self::CM_PX);
        $cy = (int) ($imageExtend->getAttribute('cy') / self::CM_EMU * self::CM_PX);
        $htmlCx = "width=\"{$cx}\"";
        $htmlCy = "height=\"{$cy}\"";

        $rels = $this->getRels();
        if (isset($rels[$imageId])) {
            $file = $this->getZipResource($rels[$imageId]);
            if ($file) {
                $ext = pathinfo($rels[$imageId], PATHINFO_EXTENSION);
                $path = $this->resourceTmpPath.'/'.Uuid::uuid4().'.'.$ext;
                file_put_contents($path, $file);
                $imageXml->nodeValue = sprintf('<img src="%s" %s %s>', $path, $htmlCx, $htmlCy);
            }
        }
    }

    /**
     * @return array
     * 获取word资源列表
     */
    protected function getRels()
    {
        $relsList = $this->relsXml->getElementsByTagName('Relationship');
        $rels = array();
        /**
         * @var $relsList \DOMNodeList
         * @var $relXml \DOMNode
         */
        foreach ($relsList as $relXml) {
            $rels[$relXml->getAttribute('Id')] = $relXml->getAttribute('Target');
        }

        return $rels;
    }

    /**
     * @param $filename
     *
     * @return false|string|null
     *
     * @throws ExamException
     *
     * 根据文件路径获取zip中对应的资源文件
     */
    protected function getZipResource($filename)
    {
        $filename = self::DOCUMENT_PREFIX.$filename;
        $path = $this->filePath;
        $zip = new ZipArchive();
        $file = null;
        if (true === $zip->open($path)) {
            if (false !== ($index = $zip->locateName($filename))) {
                $file = $zip->getFromIndex($index);
            }
            $zip->close();
        } else {
            throw new ExamException('file format is invalid');
        }

        return $file;
    }
}
