<?php

namespace ExamParser\Dumper;

interface DumperInterface
{
    public function __construct($dumpPath);

    public function dump($questions);

    public function buildStem($stem, $seq, $withAnswer = '');

    public function buildOptions($options);

    public function buildAnswer($answer);

    public function buildScore($score);

    public function buildDifficulty($difficulty);

    public function buildAnalysis($analysis);

    public function writeText($text);

    public function writeImg($src);

    public function writeTag($text);

    public function addTextBreak();
}
