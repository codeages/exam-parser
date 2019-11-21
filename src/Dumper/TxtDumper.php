<?php

namespace ExamParser\Dumper;

class TxtDumper extends BaseDumper implements DumperInterface
{
    protected $fp;

    public function __construct($dumpPath)
    {
        parent::__construct($dumpPath);
        $this->init();
    }

    public function __destruct()
    {
        fclose($this->fp);
    }

    public function dump($questions)
    {
        foreach ($questions as $question) {
            $this->getQuestionType($question['type'])->dump($question, $this);
            $this->addTextBreak();
        }
    }

    public function buildAnalysis($analysis)
    {
        if (!empty($analysis)) {
            $this->writeText('【解析】');
            $this->addTextBreak();
            foreach ($analysis as $item) {
                $this->writeIn($item['element'], $item['content']);
            }
            $this->addTextBreak();
        }
    }

    public function buildAnswer($answer)
    {
        if (!empty($answer)) {
            if (is_string($answer)) {
                $this->writeText("【答案】{$answer}");
                $this->addTextBreak();
            }

            if  (is_array($answer)) {
                $this->writeText('【答案】');
                foreach ($answer as $item) {
                    $this->writeIn($item['element'], $item['content']);
                }
                $this->addTextBreak();
            }
        }
    }

    public function buildDifficulty($difficulty)
    {
        if (!empty($difficulty)) {
            $this->writeText("【难度】{$difficulty}");
            $this->addTextBreak();
        }
    }

    public function buildOptions($options)
    {
        foreach ($options as $option) {
            foreach ($option as $item) {
                $this->writeIn($item['element'], $item['content']);
            }
            $this->addTextBreak();
        }
    }

    public function buildScore($score)
    {
        if (!empty($score)) {
            $this->writeText("【分数】{$score}");
        }
    }

    public function buildStem($stem, $seq, $withAnswer = '')
    {
        $this->writeText($seq);
        foreach ($stem as $item) {
            $this->writeIn($item['element'], $item['content']);
        }
        if (!empty($answer)) {
            $this->writeText("（{$answer}）");
        }
        $this->addTextBreak();
    }

    public function writeText($text)
    {
        if (empty($text)) {
            return;
        }
        return fwrite($this->fp, $text);
    }

    public function writeImg($src)
    {
        return ;
    }

    public function writeTag($text)
    {
        $text = strip_tags($text);
        $text = trim($text).PHP_EOL;

        if (empty($text)) {
            return;
        }

        return fwrite($this->fp, $text);

    }

    public function addTextBreak()
    {
        fwrite($this->fp, PHP_EOL);
    }

    protected function writeIn($element, $content)
    {
        $method = 'write'.ucfirst($element);
        $this->$method($content);
    }

    protected function init()
    {
        $this->fp = fopen($this->filename, 'w');
    }
}
