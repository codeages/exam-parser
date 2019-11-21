<?php

namespace ExamParser\QuestionType;

use ExamParser\Dumper\DumperInterface;

class SingleChoice extends Choice
{
    public function dump($item, DumperInterface $dumper)
    {
        if ('single_choice' != $item['type']) {
            return;
        }

        $dumper->buildStem($item['stem'], $item['num']);
        $dumper->buildOptions($item['options']);
        $dumper->buildAnswer($item['answer']);
        $this->dumpCommonModule($item, $dumper);
    }
}
