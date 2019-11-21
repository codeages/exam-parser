<?php

namespace ExamParser\Tests;

use ExamParser\Parser\FileReader\ReaderFactory;
use ExamParser\Constants\ParserType;

class ReaderFactoryTest extends BaseTestCase
{
    public function testCreateReader()
    {
        $reader = ReaderFactory::createReader(__DIR__.'/Fixtures/files/docx/questions.docx', array());
        $this->assertInstanceOf('ExamParser\\Parser\\FileReader\\ReaderInterface', $reader);

    }
}
