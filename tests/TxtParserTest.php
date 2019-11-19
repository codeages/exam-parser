<?php

namespace ExamParser\Tests;

use ExamParser\Parser\TxtParser;

class TxtParserTest extends BaseTestCase
{
    public function testRead()
    {
        $parser = new TxtParser();
        $content = $parser->read(__DIR__.'/Fixtures/files/txt/question_whole.txt');
        $this->assertNotEmpty($content);
    }

    public function testParser()
    {
        $parser = new TxtParser();
        $content = $parser->read(__DIR__.'/Fixtures/files/txt/question_whole.txt');
        $questions = $parser->parser($content);
        $this->assertCount(7, $questions);
    }
}
