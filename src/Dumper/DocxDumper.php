<?php

namespace ExamParser\Dumper;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class DocxDumper extends BaseDumper implements DumperInterface
{
    /**
     * @var PhpWord
     */
    protected $phpWord;

    /**
     * @var \PhpOffice\PhpWord\Element\Section
     */
    protected $section;

    /**
     * @var \PhpOffice\PhpWord\Element\TextRun
     */
    protected $textRun;

    public function __construct($dumpPath)
    {
        parent::__construct($dumpPath);
        $this->init();
    }

    public function dump($questions)
    {
        $phpWord = $this->getPhpWord();
        foreach ($questions as $question) {
            $this->getQuestionType($question['type'])->dump($question, $this);
            $this->section->addTextBreak();
        }
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($this->filename);
    }

    public function buildAnalysis($analysis)
    {
        if (!empty($analysis)) {
            $this->useTextRun();
            $this->writeText('【解析】');
            foreach ($analysis as $item) {
                $this->writeIn($item['element'], $item['content']);
            }
            $this->cancelTextRun();
        }
    }

    public function buildAnswer($answer)
    {
        if (!empty($answer)) {
            if (is_string($answer)) {
                $this->writeText("【答案】{$answer}");
            }

            if  (is_array($answer)) {
                $this->useTextRun();
                $this->writeText('【答案】');
                foreach ($answer as $item) {
                    $this->writeIn($item['element'], $item['content']);
                }
                $this->cancelTextRun();
            }
        }
    }

    public function buildDifficulty($difficulty)
    {
        if (!empty($difficulty)) {
            $this->writeText("【难度】{$difficulty}");
        }
    }

    public function buildOptions($options)
    {
        foreach ($options as $option) {
            $this->useTextRun();
            foreach ($option as $item) {
                $this->writeIn($item['element'], $item['content']);
            }
            $this->cancelTextRun();
        }
    }

    public function buildScore($score)
    {
        if (!empty($score)) {
            $this->writeText("【分数】{$score}");
        }
    }

    public function buildStem($stem, $seq, $answer = '')
    {
        $this->useTextRun();
        $this->writeText($seq);
        foreach ($stem as $item) {
            $this->writeIn($item['element'], $item['content']);
        }
        if (!empty($answer)) {
            $this->writeText("（{$answer}）");
        }
        $this->cancelTextRun();
    }

    public function writeText($text)
    {
        $text = strip_tags($text);
        $text = str_replace(array("\n", "\r", "\t"), '<w:br/>', $text);
        $text = str_replace('&nbsp;', ' ', $text);
        $text = str_replace('&', '&amp;', $text);
        $text = trim($text);

        if (empty($text)) {
            return;
        }

        if (empty($this->textRun)) {
            $this->section->addText($text);
        } else {
            $this->textRun->addText($text);
        }
    }

    public function writeImg($src)
    {
        if (empty($this->textRun)) {
            $this->section->addImage($src);
        } else {
            $this->textRun->addImage($src);
        }
    }

    public function writeTag($text)
    {
        $text = strip_tags($text);
        $text = trim($text);

        if (empty($text)) {
            return;
        }

        if (empty($this->textRun)) {
            $this->section->addText($text);
        } else {
            $this->textRun->addText($text);
        }
    }

    public function addTextBreak()
    {
        $this->section->addTextBreak();
    }

    protected function useTextRun()
    {
        $this->textRun = $this->section->addTextRun();
    }

    protected function cancelTextRun()
    {
        $this->textRun = null;
    }

    protected function writeIn($element, $content)
    {
        $method = 'write'.ucfirst($element);
        $this->$method($content);
    }

    protected function init()
    {
        $phpWord = $this->getPhpWord();
        $this->section = $phpWord->addSection();
    }
    protected function getPhpWord()
    {
        return $this->phpWord ? : $this->phpWord = new PhpWord();
    }
}
