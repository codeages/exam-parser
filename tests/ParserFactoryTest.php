<?php

namespace ExamParser\Tests;

use ExamParser\Parser\DocxParser;
use ExamParser\Parser\ParserFactory;
use ExamParser\Constants\ParserType;

class ParserFactoryTest extends BaseTestCase
{
    public function testCreateParser()
    {
        $parser = ParserFactory::createParser(ParserType::DOCX);
        $this->assertInstanceOf('ExamParser\\Parser\\ParserInterface', $parser);

    }
}
