<?php

namespace ExamParser\Output;

interface OutputInterface
{
    public function buildStem($stem);

    public function buildOptions($options);

    public function buildAnswer($answer);

    public function buildScore($score);

    public function buildDifficulty($difficulty);

    public function buildAnalysis($analysis);
}