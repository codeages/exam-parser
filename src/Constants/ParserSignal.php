<?php

namespace ExamParser\Constants;

class ParserSignal
{
    const START_SIGNAL = '【导入开始】';

    const MATERIAL_START_SIGNAL = '【材料题开始】';

    const CODE_MATERIAL_START_SIGNAL = '<#材料题开始#>';

    const MATERIAL_END_SIGNAL = '【材料题结束】';

    const CODE_MATERIAL_END_SIGNAL = '<#材料题结束#>';

    const CODE_MATERIAL_SUB_QUESTION_START = '<#材料题子题#>';

    const UNCERTAIN_CHOICE_SIGNAL = '【不定项选择题】';

    const CODE_UNCERTAIN_CHOICE_SIGNAL = '<#不定项选择题#>';
}
