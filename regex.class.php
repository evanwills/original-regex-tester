<?php
/**
 * This file contains a collection of four PREG Regex related classes
 * for testing, matching, find/replacing and getting regex errors
 *
 * The primary aim of this set of classes is to provide feedback to
 * users about their regular expressions in systems that allow users
 * to input their own regexes. It is assumed that these classes will
 * only be used in the user interface of a system and not when the
 * system is actually applying the regex. An invalid regular
 * expression is of no use to anybody and should not be stored by a
 * system. Also these classes incur an additional overhead
 * unnecessary when using a valid regex.
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
 * Regex is a factory class with a public factory method which
 * returns the appropriate child regex class
 *
 * This class provides the basic infrastructure for the child
 * regex classes along with a static method for validating regular
 * expressions.
 *
 * PHP Version 5.4, 7.x, 8.0
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class Regex
{
    const REGEX_FIND_ESCAPED = '/^(.).*?(?<=(?<=\\\\\\\\)|(?<!\\\\))\1(.*)$/is';

    /**
     * Regular expression to be used
     *
     * @var string $find
     */
    protected $find = '';

    /**
     * Replacement pattern
     *
     * @var string $replace
     */
    protected $replace = '';

    /**
     * List of errors caused by the regular expression
     *
     * @var array $error
     */
    protected $errors = array();

    /**
     * Regex object type
     *
     * @var string $type
     */
    protected $type = 'regex';

    /**
     * List of errors caused at a programatic level by bad input
     * values
     *
     * @var array $inputErrors
     */
    protected $inputErrors = array();

    /**
     * Maximum number of characters displayed in the 'sample' index
     * in the report output array
     *
     * @var integer $sampleLen
     */
    protected static $sampleLen = 300;

    /**
     * Number of characters from the start of a string shown before
     * appending an elipsus to the truncated sample string and then
     * displayed in the 'sample' index in the report output array
     *
     * @var integer $sampleLenDot
     */
    protected static $sampleLenDot = 297;

    /**
     * An object for doing calculations with large floating point
     * numbers
     *
     * @var object $MicroTime
     */
    protected static $MicroTime = null;

    /**
     * Constructor
     *
     * @param string $find    Valid regular expression to use for
     *                        matching
     * @param string $replace Replacement string
     * @param array  $errors  If there are errors in the regex then
     *                        RegexError::__construct() will use the
     *                        error array to generate useful feedback
     *                        about what is wrong with the regex
     */
    protected function __construct($find, $replace = false, $errors = false)
    {
        // NOTE: It makes no sence within the context of this class
        //       to allow the 'e' (evaluate) modifier so we is
        //       stripped from modifiers part of the regex
        if (empty($errors)
            && preg_match(self::REGEX_FIND_ESCAPED, $find, $matches)
        ) {
            $find = preg_replace(
                '/' . $matches[2] . '$/',
                str_replace('e', '', $matches[2]),
                $find
            );
        }
        $this->find = $find;
        $this->highlighted = '<span class="ok">' .
                                 htmlspecialchars($find) .
                             '</span>';
        $this->replace = $this->fixLineEnd($replace);
        $this->errors = $errors;

        if (is_null(self::$MicroTime)) {
            self::$MicroTime = MicroTime::getObj();
        }
    }

    /**
     * Takes a given regular expression and return the appropriate
     * regex object
     *
     * @param string $find    Regular expression to be used
     * @param string $replace Pattern/string to replace matched string
     *
     * @return object appropriate regex object:
     *                * If regex has a replace a RegexReplace object
     *                  is returned
     *                * If regex has an error a RegexError object is
     *                  returned otherwise a RegexMatch object is
     *                  returned
     */
    public static function process($find, $replace = false)
    {
        $errors = array();
        if (!is_string($find)) {
            $errors[] = 'regex::process() first paramater must be '.
                        'a string. '.gettype($find).' given!';
            // throw
        }
        if (!is_string($replace) && $replace !== false) {
            $errors[] = 'regex::process() second paramater must be '.
                        'a string (or void). '.gettype($find).' given!';
            // throw
        }

        // turn PHP errors on or clear errors
        $tmp = self::validRegex($find);
        if ($tmp !== true) {
            $errors = array_merge($errors, $tmp);
        }

        if (empty($errors)) {
            if ($replace === false) {
                return new RegexMatch($find);
            } else {
                return new RegexReplace($find, $replace);
            }
        } else {
            return new RegexError($find, $replace, $errors);
        }
    }

    /**
     * Check whther a given regex is valid
     *
     * @param string $find Regex string to be validated
     *
     * @return true|array TRUE if regex is valid. Array of error
     *                    message strings if regex is not valid
     */
    static public function validRegex($find)
    {
        $find = trim($find);
        $errors = array();

        if (!is_string($find) || $find == '') {
            return array('Not a string or empty string');
        }

        if (empty($errors)) {
            if ($old_track_errors = ini_get('track_errors')) {
                $old_php_errormsg = isset($php_errormsg) ? $php_errormsg : false;
            } else {
                ini_set('track_errors', 1);
            }
            unset($php_errormsg);

            $display_errors = ini_get('display_errors');
            ini_set('display_errors', 'stderr');

            $html_errors = ini_get('html_errors');
            ini_set('html_errors', 'false');

            preg_match($find, '');

            ini_set('display_errors', $display_errors);
            ini_set('html_errors', $html_errors);

            unset($display_errors, $html_errors);

            if (isset($php_errormsg)) {
                $php_errormsg = str_replace('preg_match(): ', '', $php_errormsg);
                $errors[] = $php_errormsg;
                unset($php_errormsg);
            }
            if ($old_track_errors) {
                $php_errormsg = isset($old_php_errormsg) ? $old_php_errormsg : false;
            } else {
                ini_set('track_errors', 0);
            }
        }

        if (empty($errors)) {
            return true;
        } else {
            return $errors;
        }
    }

    /**
     * Get information about what the regex matched in the sample
     * string
     *
     * @param string $sample Text the regex is to be applied to
     *
     * @return array Array containing five associative values:
     *               {string}  'output'  Array of information about
     *                                   the regex in relation to the
     *                                   sample
     *               {float}   'time'    Time taken to execute the
     *                                   regex on the sample
     *               {integer} 'count'   The number of matches the
     *                                   regex had on the sample
     *               {string}  'regex'   Regular expression used
     *               {string}  'replace' Replacement pattern/string
     *                                   used
     */
    public function report($sample)
    {
        $sample_str = $this->validInput($sample);
        if ($sample_str === false) {
            $sample_str = '';
        }
        return array_merge(
            array(
                'output' => array($sample),
                'time' => '-1',
                'count' => -1,
                'regex' => $this->find,
                'sample' => $sample_str,
                'replace' => $this->replace,
                'type' => $this->type
            ),
            $this->getErrors()
        );
    }

    /**
     * Get whether the object holds a valid regex
     *
     * NOTE: RegexMatch and RegexReplace contain only valid regular
     *       expressions, where-as RegexError contains only invalid
     *       regular expressions)
     *
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Look at how to break up the regex or how to fix a broken regex
     *
     * @return array
     */
    public function alalyse()
    {
        return array();
    }

    /**
     * Get the sample input after being passed through preg_replace
     *
     * @param string $sample text the regex is to be applied to
     *
     * @return array Array with three associative values:
     *               {string}  'output' String processed sample
     *               {float}   'time'   The time taken to execute the
     *                                  regex on the sample
     *               {integer} 'count'  Number of matches the regex
     *                                  had on the sample
     */
    public function getOutput($sample)
    {
        return $sample;
    }


    /**
     * Get all error information generated by the object, including
     * input and preg errors
     *
     * @return array an array of strings with error messages.
     */
    public function getErrors()
    {
        return array(
            'error_message' => 'No errors',
            'error_highlighted' => '<span class="ok">' .
                                       $this->find .
                                   '</span>'
        );
    }

    /**
     * Get the regex this object holds
     *
     * @param boolean $highlighted Whether or not to return to syntax
     *                             highlight returned regex
     *
     * @return string regular expression used in the object
     */
    public function getRegex($highlighted = false)
    {
        if ($highlighted === true) {
            return $this->highlighted;
        } else {
            return $this->find;
        }
    }

    /**
     * Get the replacement string this object holds
     *
     * @return string replacement string used in the object (if any)
     */
    public function getReplace()
    {
        return $this->replace;
    }

    /**
     * Check whether a given method's input is a string
     *
     * @param mixed $input Input to be validated
     *
     * @return string
     */
    public function validInput($input)
    {
        if (!is_string($input)) {
            $backtr = debug_backtrace();
            $obj =  $backtr[1]['class'];
            $func = $backtr[1]['function'];
            $this->inputErrors[] = "$obj::$func() first paramater ".
                                   "must be a string. ".
                                   gettype($input).' given!';
            return false;
        } else {
            if (strlen($input) > regex::$sampleLen) {
                return substr($input, regex::$sampleLenDot) . '...';
            } else {
                return $input;
            }
        }
    }


    /**
     * Set the maximum length of the sample before it's truncated
     * in the returned output of the regex::report() array.
     *
     * @param integer $len a number greater than 3 [default: 300]
     *
     * @return boolean TRUE if regex::sample_len is updated. FALSE
     *                 otherwise.
     */
    public static function setSampleLen($len)
    {
        if (!is_int($len) || $len < 3) {
            // throw
            return false;
        } else {
            regex::$sampleLen = $len;
            regex::$sampleLenDot = ($len - 3);
            return true;
        }
    }

    /**
     * Shortcut method for htmlspecialchars()
     *
     * @param string $input String with HTML characters escaped
     *
     * @return string Escaped string
     */
    protected function h($input)
    {
        return htmlspecialchars($input);
    }

    /**
     * Convert white space escape sequences to normal white space
     * characters
     *
     * @param string $input String to be converted
     *
     * @return string converted string
     */
    protected function fixLineEnd($input)
    {
        $find = array(
            '/(?<![^\\\\])\\\\r/',
            '/(?<![^\\\\])\\\\n/',
            '/(?<![^\\\\])\\\\t/'
            // '/(?<![^\\\\])\\\\[rnt]/'
        );
        $replace = array(
            "\r", "\n", "\t"
        );
        return preg_replace($find, $replace, $input);
    }
}




// +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+




/**
 * RegexMatch provides feedback on how a regular expression
 * will behave when applied to a given string.
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexMatch extends regex
{
    /**
     * Regex object type
     *
     * @var string $type
     */
    protected $type = 'match';

    /**
     * Get information about what the regex matched in the sample
     * string
     *
     * @param string $sample Text the regex is to be applied to
     *
     * @return array Array containing five associative values:
     *               {string}  'output'  Array of information about
     *                                   the regex in relation to the
     *                                   sample
     *               {float}   'time'    Time taken to execute the
     *                                   regex on the sample
     *               {integer} 'count'   The number of matches the
     *                                   regex had on the sample
     *               {string}  'regex'   Regular expression used
     *               {string}  'replace' Replacement pattern/string
     *                                   used
     */
    public function report($sample)
    {
        $sample_str = $this->validInput($sample);
        if ($sample_str !== false) {
            $start = microtime();
            preg_match_all($this->find, $sample, $matches, PREG_SET_ORDER);
            $end = microtime();
            $time = self::$MicroTime->mtSub($start, $end);
            $count = count($matches);
        } else {
            $matches = $this->inputErrors;
            $time = '-1';
            $count = -1;
            $sample_str = '';
        }

        $output = array_merge(
            array(
                'output' => $matches,
                'time' => $time,
                'count' => $count,
                'regex' => $this->find,
                'sample' => $sample_str,
                'replace' => $this->replace,
                'type' => $this->type
            ),
            $this->getErrors()
        );
        return $output;
    }
}




// +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+




/**
 * RegexReplace provides feedback on how a regular expression
 * and associated replace string will behave when applied to a given
 * string.
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexReplace extends RegexMatch
{
    /**
     * Regex object type
     *
     * @var string $type
     */
    protected $type = 'replace';


    /**
     * Get the sample input after being passed through preg_replace
     *
     * @param string $sample text the regex is to be applied to
     *
     * @return array Array with three associative values:
     *               {string}  'output' String processed sample
     *               {float}   'time'   The time taken to execute the
     *                                  regex on the sample
     *               {integer} 'count'  Number of matches the regex
     *                                  had on the sample
     */
    public function getOutput($sample)
    {
        $sample_str = $this->validInput($sample);
        if ($sample_str !== false) {
            $start = microtime();
            $output = preg_replace($this->find, $this->replace, $sample, -1, $count);
            $end = microtime();
            $time = self::$MicroTime->mtSub($start, $end);
        } else {
            $output = $this->errors;
            $time = '-1';
            $count = -1;
        }

        return $output;
    }
}




// +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+




/**
 * RegexError provides feedback on what is wrong with a regular
 * expression
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexError extends regex
{
    const REGEX_ERROR_MISSING_TERM
        = '/missing (?:terminating )?([\]\}\)]) .*? offset ([0-9]+)/';
    const REGEX_ERROR_LOOK_BEHIND
        = '/lookbehind assertion is not fixed length at offset ([0-9]+)/is';
    const REGEX_ERROR_NO_END_DELIM
        = '/No ending (?:matching )?delimiter \'([^\']+)\' found/is';
    const REGEX_ERROR_BAD_DELIM
        = '/Delimiter must not be alphanumeric or backslash/i';

    /**
     * Regex object type
     *
     * @var string $type
     */
    protected $type = 'error';

    /**
     * Identifies whether an error message has been processed
     *
     * @var boolean $_errorProcessed
     */
    private $_errorProcessed = false;



    /**
     * Get information about what the regex matched in the sample
     * string
     *
     * @param string $sample text the regex is to be applied to
     *
     * @return array Array containing five associative values:
     *               {string}  'output'  Array of information about
     *                                   the regex in relation to the
     *                                   sample
     *               {float}   'time'    Time taken to execute the
     *                                   regex on the sample
     *               {integer} 'count'   The number of matches the
     *                                   regex had on the sample
     *               {string}  'regex'   Regular expression used
     *               {string}  'replace' Replacement pattern/string
     *                                   used
     */
    public function report($sample)
    {
        $sample_str = $this->validInput($sample);
        if ($sample_str === false) {
            $sample_str = '';
        }
        $output = array_merge(
            array(
                'output' => array(),
                'time' => "-1",
                'count' => -1,
                'regex' => $this->find,
                'sample' => $sample_str,
                'replace' => $this->replace,
                'type' => $this->type
            ),
            $this->getErrors()
        );

        return $output;
    }


    /**
     * Get all error information generated by the object, including
     * input and preg errors
     *
     * @return array an array of strings with error messages.
     */
    public function getErrors()
    {
        if ($this->_errorProcessed === false) {
            $e_count = count($this->errors) - 1;
            $error = $this->errors[$e_count];
            $error_wrap = array('open' => '', 'close' => '');

            if (preg_match(self::REGEX_ERROR_MISSING_TERM, $error, $matches)) {
                $bracket = $matches[1];
                $offset = ++$matches[2];

                preg_match('/^(.{' . $offset . '})(.*)$/is', $this->find, $matches);
                $head = $matches[1];
                $tail = $matches[2];
                switch ($bracket) {
                case ']':
                    $bracket_ = '[';
                    break;
                case '}':
                    $bracket_ = '{';
                    break;
                case ')':
                    $bracket_ = '(';
                    break;
                case '[':
                    $bracket_ = ']';
                    break;
                case '{':
                    $bracket_ = '}';
                    break;
                case '(':
                    $bracket_ = ')';
                    break;
                }
                preg_match('/^(.*?\\' . $bracket_ . ')(.*)$/is', $head, $matches);
                $start = $matches[1];
                $middle = $matches[2];

                $this->highlight = $this->_getHighlighted(
                    $matches[1], $matches[2], $tail
                );
                // $this->highlight = '<span class="ok">' . $this->h($matches[1]) .
                //                    '</span><span class="problem">' .
                //                    $this->h($matches[2]) .
                //                    '</span><span class="error">' .
                //                    $this->h($tail) . '</span>';
            } elseif (preg_match('/Unknown modifier/is', $error)) {
                preg_match('/^./', $this->find, $matches);
                switch ($matches[0]) {
                case '(':
                    $wrap = ')';
                    break;
                case '[':
                    $wrap = ']';
                    break;
                case '{':
                    $wrap = '}';
                    break;
                case '<':
                    $wrap = '>';
                    break;
                default:
                    $wrap = $matches[0];
                }

                $regex = '/^.(.*?)(?<!\\\\)(\\' . $wrap . ')(.*)$/s';

                preg_match($regex, $this->find, $matches);

                $this->highlight = $this->_getHighlighted(
                    $matches[1], $matches[2], $matches[3]
                );
            } elseif (preg_match('/at offset ([0-9]+)/is', $error, $matches)) {
                $offset = ++$matches[1];

                preg_match(
                    '/^(.{' . $offset . '})(.)(.*)$/is',
                    $this->find,
                    $matches
                );

                $this->highlight = $this->_getHighlighted(
                    $matches[1], $matches[2], $matches[3]
                );
            } elseif (preg_match(self::REGEX_ERROR_LOOK_BEHIND, $error, $matches)) {
                $offset = ++$matches[1];

                preg_match('/^(.{' . $offset . '})(.*)$/is', $this->find, $matches);
                $head = $matches[1];
                $tail = $matches[2];
                preg_match('/^(.*)(\(\?<[=!].*)/is', $head, $matches);

                $this->highlight = $this->_getHighlighted(
                    $matches[1], $matches[2], $tail
                );
            } elseif (preg_match(self::REGEX_ERROR_NO_END_DELIM, $error, $matches)) {
                $delim = $matches[1];
                preg_match('/^(.)(.*)$/is', $this->find, $matches);

                $this->highlight = $this->_getHighlighted(
                    $matches[1], $matches[2]
                );
            } elseif (preg_match(self::REGEX_ERROR_BAD_DELIM, $error, $matches)) {
                preg_match('/^(.)(.*)$/is', $this->find, $matches);

                $this->highlight = $this->_getHighlighted(
                    $matches[1], $matches[2]
                );
            } else {
                $error_wrap = array(
                    'open' => '<span class="unknown"',
                    'close' => '</span>'
                );
                // die(
                //     'PREG encountered an error I couldn\'t recognise '.
                //     '(or at least haven\'t seen yet): "'.$error.'"'
                // );
            }
            $error = str_replace(
                'preg_match() [function.preg-match]: ',
                '',
                strip_tags($error)
            );
            $this->errors = array(
                'error_message' => $error_wrap['open'] . $error .
                                 $error_wrap['close'],
                'error_highlight' => $this->highlight
            );
            $this->_errorProcessed = true;
        }
        return $this->errors;
    }

    /**
     * Whether the object holds a valid regex
     *
     * @return boolean false (RegexError only contains invalid objects)
     */
    public function isValid()
    {
        return false;
    }

    /**
     * Render HTML for regular expression with error portion
     * highlighted
     *
     * @param string $ok      Initial part of the regex before the
     *                        problem occured
     * @param string $error   Part of the regular expression causing
     *                        the problem
     * @param string $problem Everything that comes after the problem
     *
     * @return string HTML formatted regex
     */
    private function _getHighlighted($ok, $error, $problem = '')
    {
        if ($problem !== '') {
            $problem =  '<span class="problem">'.
                            $this->h($problem).
                        '</span>';
        }
        return  '<span class="ok">'.
                        $this->h($ok).
                '</span>'.
                '<span class="error">'.
                    $this->h($error).
                '</span>'. $problem;
    }
}


if (!class_exists('MicroTime')) {
    include dirname(__FILE__) . '/MicroTime.class.php';
}
