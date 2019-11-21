<?php

namespace ExamParser\Tests;

use ExamParser\Parser\Parser;

class TxtParserTest extends BaseTestCase
{
    public function testParser()
    {
        $parser = new Parser();
        $content = $parser->parse(__DIR__.'/Fixtures/files/txt/question_whole.txt');
        $this->assertNotEmpty($content);
    }
}
