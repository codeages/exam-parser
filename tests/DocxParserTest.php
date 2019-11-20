<?php

namespace ExamParser\Tests;

use ExamParser\Parser\DocxParser;

class DocxParserTest extends BaseTestCase
{
    public function testRead()
    {
        $parser = new DocxParser();
        $content = $parser->read(__DIR__.'/Fixtures/files/docx/questions.docx');
        $this->assertNotEmpty($content);
    }

    public function testParser()
    {
        $parser = new DocxParser();
        $content = $parser->read(__DIR__.'/Fixtures/files/docx/questions.docx');
        $questions = $parser->parser($content);
        $this->assertCount(7, $questions);
    }
}
