<?php

namespace ExamParser\Parser\QuestionType;

abstract class AbstractQuestion
{
    const ANSWER_SIGNAL = '【答案】';

    const DIFFICULTY_SIGNAL = '【难度】';

    const SCORE_SIGNAL = '【分数】';

    const ANALYSIS_SIGNAL = '【解析】';

    abstract public function convert();
}
