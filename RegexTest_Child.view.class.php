<?php

abstract class RegexTest_ChildView
{

    static protected $sampleLen = 300;
    static protected $matchedLen = 300;


    abstract public function getRegexFieldsetItem(
        $index,
        RegexTest_ChildModel $model
    );

    /**
     * Format information generated regex::report() and any feedback
     * from adding/updating/deleting an archive
     *
     * @param RegexTest_ChildModel $model Object containing all
     *                                       info on regex processed
     *
     * @return string Formatted contents of report
     *                (including archiver feedback)
     */
    abstract public function formatReport(RegexTest_ChildModel $model);

    public static function setLen($input, $type = false)
    {
        if (is_int($input) && $input > 6) {
            if ($type !== 'matched') {
                self::$sampleLen = $input;
            }
            if ($type !== 'sample') {
                self::$matchedLen = $input;
            }
            return true;
        }
        return false;
    }

    protected function trimString($input, $type = 'sample')
    {
        if ($type === 'matched') {
            $len = self::$matchedLen;
        } else {
            $len = self::$sampleLen;
        }
        if (strlen($input) > $len) {
            $len -= 3;
            return substr($input, 0, $len) . '...';
        }
        return $input;
    }
}
