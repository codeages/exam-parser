<?php

namespace ExamParser\QuestionType;

use ExamParser\Dumper\DumperInterface;

class UncertainChoice extends Choice
{
    public function dump($item, DumperInterface $dumper)
    {
        if ('uncertain_choice' != $item['type']) {
            return;
        }

        $dumper->writeTag('【不定项选择题】');
        $dumper->buildStem($item['stem'], $item['num']);
        $dumper->buildOptions($item['options']);
        $dumper->buildAnswer($item['answer']);
        $this->dumpCommonModule($item, $dumper);
    }
}
