<?php

namespace ExamParser\Tests;

use ExamParser\Parser\Parser;

class DocxParserTest extends BaseTestCase
{
    public function testParser()
    {
        $parser = new Parser();
        $content = $parser->parse(__DIR__.'/Fixtures/files/docx/questions.docx');
        $this->assertNotEmpty($content);
    }
}
