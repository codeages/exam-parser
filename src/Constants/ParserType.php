<?php

namespace ExamParser\Constants;

class ParserType
{
    /**
     * TXT text
     */
    const TXT = 'txt';

    /**
     * Word 1999-2003
     */
    const DOC = 'doc';

    /**
     * Word 2007
     */
    const DOCX = 'docx';

    public static function types()
    {
        return array(
            self::TXT,
            self::DOC,
            self::DOCX,
        );
    }
}
