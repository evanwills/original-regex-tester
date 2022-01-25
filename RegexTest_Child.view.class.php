<?php
/**
 * This file contains a single class for rendering the content of a
 * single regex pair.
 *
 * PHP Version 5.4, 7.x, 8.0
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */

/**
 * Child View HTML prepares provides helper methods and two abstrack
 * method definitions used by inheriting classes to render the
 * contents of a single regex pair
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
abstract class RegexTest_ChildView
{

    static protected $sampleLen = 300;
    static protected $matchedLen = 300;

    /**
     * Get the HTML for a given Regex pair
     *
     * @param integer              $index Index of pair to be returned
     * @param RegexTest_ChildModel $model Regex pair object
     *
     * @return string
     */
    abstract public function getRegexFieldsetItem(
        $index,
        RegexTest_ChildModel $model
    );

    /**
     * Format information generated regex::report() and any feedback
     * from adding/updating/deleting an archive
     *
     * @param RegexTest_ChildModel $model Object containing all
     *                                    info on regex processed
     *
     * @return string Formatted contents of report
     *                (including archiver feedback)
     */
    abstract public function formatReport(RegexTest_ChildModel $model);

    /**
     * Set the max length for either sample or matched strings
     *
     * @param integer $input The maximum allowable length
     * @param boolean $type  type of string length is set for
     *
     * @return boolean TRUE if length was set. FALSE otherwise
     */
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

    /**
     * Truncate a given string if it's longer than the maximum
     * allowed length for that type of string
     *
     * @param string $input String to be truncated
     * @param string $type  Type of string being truncated
     *
     * @return string
     */
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
