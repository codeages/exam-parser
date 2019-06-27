<?php

namespace ExamParser\Tests\Reader;

use ExamParser\Reader\ReadDocx;
use ExamParser\Tests\BaseTestCase;
use PhpOffice\PhpWord\IOFactory;

class ReaderDocxTest extends BaseTestCase
{
    public function testRead()
    {
        $filename = dirname(__DIR__).'/Fixtures/files/example1.docx';
        $wordRead = new ReadDocx($filename);
    }

    public function testReadDoc()
    {
        $filename = dirname(__DIR__).'/Fixtures/files/example1.docx';
        $wordRead = new ReadDocx($filename);
//        $wordRead->setDocx($filename);
//        $writer = IOFactory::createWriter($wordRead->getPhpWprd(), 'HTML');
//
//        $tmpName = '/tmp/'.time().'.text';
//
//        $writer->save($tmpName);
    }

    public function testConvertImage()
    {
        $fileName = $filename = dirname(__DIR__).'/Fixtures/files/example1.docx';
        $wordRead = new ReadDocx($filename);
        $wordRead->convertImage();
    }
}
