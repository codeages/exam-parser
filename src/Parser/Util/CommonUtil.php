<?php

namespace ExamParser\Parser\Util;

class CommonUtil
{
    //下划线命名到驼峰命名
    public static function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = '';
        $len = count($array);
        for ($i = 0; $i < $len; ++$i) {
            $result .= ucfirst($array[$i]);
        }

        return $result;
    }
}