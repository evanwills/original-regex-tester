<?php
/**
 * This file contains a single model classes for holding individual
 * regex pairs
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
 * RegexTest_ChildModel holds all the data for a single regex pair
 * including:
 * * the regex itself
 * * its replacement pattern
 * * its delimiters
 * * and its modifiers
 *   plus
 * * any error messages the regex may have generated and
 * * any reportable information
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexTest_ChildModel
{
    private $_regex;
    private $_find = '';
    private $_replace = '';
    private $_modifiers = '';
    private $_modifiers_original = '';
    private $_multiline = false;
    private $_errors = array('modifiers' => '');
    private $_report = array();

    private static $_delim_open = '`';
    private static $_delim_close = '`';


    /**
     * Constructor
     *
     * @param string  $find      Regex pattern
     * @param string  $replace   Replacement pattern
     * @param string  $modifiers Regex modifier characters
     * @param boolean $multiline Whether the regex input was
     *                           multi-line
     *
     * @return void
     */
    public function __construct($find, $replace, $modifiers, $multiline)
    {
        if (!is_string($find)) {
            $this->_errors[] = '$_find is not a string';
        }
        if (!is_string($replace)) {
            $this->_errors[] = '$_replace is not a string';
        }
        $this->_find = $find;
        $this->_replace = $replace;

        if (is_string($modifiers)) {
            $this->_modifiers_original = $modifiers;
            $modifiers = str_split($modifiers);
            for ($a = 0; $a < count($modifiers); $a += 1) {
                switch ($modifiers[$a]) {
                case 'i':
                case 'm':
                case 's':
                case 'x':
                case 'e':
                case 'A':
                case 'D':
                case 'S':
                case 'U':
                case 'X':
                case 'u':
                    $this->_modifiers .= $modifiers[$a];
                    break;
                default:
                    $this->_errors['modifiers'] .= '"'.$modifiers[$a].
                                                   '" is not a valid modifier. ';
                }
            }
        }
        if (is_bool($multiline)) {
            $this->_multiline = $multiline;
        }

        $this->_regex = RegexReplace::process($this->getRegex(), $replace);
    }


    /**
     * Whether or not the input for the regex was multi line
     *
     * @return boolean
     */
    public function getMultiLine()
    {
        return $this->_multiline;
    }

    /**
     * Whether or not the regex is valid
     *
     * @return boolean
     */
    public function regexIsValid()
    {
        return $this->_regex->isValid();
    }

    /**
     * Get the full regex string with delimiters and modifiers
     *
     * @return string
     */
    public function getRegex()
    {
        return self::$_delim_open . $this->_find .
               self::$_delim_close . $this->_modifiers;
    }

    /**
     * Get the raw regex string excluding delimiters and modifiers
     *
     * @return string
     */
    public function getFind()
    {
        return $this->_find;
    }

    /**
     * Get the regex replacement pattern
     *
     * @return string
     */
    public function getReplace()
    {
        return $this->_replace;
    }

    /**
     * Get the regex modifiers characters only
     *
     * @param boolean $original Whether or not to return the supplied
     *                          modifiers including possible bad
     *                          modifiers
     *
     * @return string
     */
    public function getModifiers($original = true)
    {
        if ($original !== false) {
            return $this->_modifiers_original;
        } else {
            return $this->_modifiers;
        }
    }

    /**
     * Process a sample string against this regex pair
     *
     * @param string|array $sample Sample(s) to be processed
     *
     * @return array Array containing the modified sample string the
     *               total time it took to process the string and the
     *               number of matches the regex made
     */
    public function process($sample)
    {
        if (is_array($sample)) {
            for ($a = 0; $a < count($sample); $a += 1) {
                $this->report[] = $this->regex->report($sample[$a]);
                $sample[$a] = $this->regex->getOutput($sample[$a]);
            }
            return $sample;
        } else {
            $this->report[] = $this->regex->report($sample);
            return $this->regex->getOutput($sample);
        }
    }

    /**
     * Get any errors this regex may have generated.
     *
     * @return array
     */
    public function getErrors()
    {
        return array_merge($this->_errors, $this->regex->getErrors());
    }

    /**
     * Get the report on what the regex
     *
     * @return array
     */
    public function getReport()
    {
        return $this->_report;
    }

    /**
     * Set shared delimiter character for all regexes
     *
     * @param string $delim Single character to use as preg delimiter
     *
     * @return boolean TRUE if the delimiter was set. FALSE otherwise
     */
    public static function setRegexDelim($delim)
    {
        if (is_string($delim) && strlen($delim) == 1
            && preg_match('`^[^a-z0-9\s\\\\]$`', $delim)
        ) {
            switch ($delim) {
            case '{':
            case '}':
                self::$_delim_open = '{';
                self::$_delim_close = '}';
                break;
            case '[':
            case ']':
                self::$_delim_open = '[';
                self::$_delim_close = ']';
                break;
            case '<':
            case '>':
                self::$_delim_open = '<';
                self::$_delim_close = '>';
                break;
            case '(':
            case ')':
                self::$_delim_open = '(';
                self::$_delim_close = ')';
                break;
            default:
                    self::$_delim_open = self::$_delim_close = $delim;
            }
            return true;
        }
        return false;
    }

    /**
     * Get regex delimiter
     *
     * @return string
     */
    public static function getRegexDelim()
    {
        return self::$_delim_open;
    }

    /**
     * Set shared delimiter character for all regexes
     *
     * (Shortcut alias for RegexTest_ChildModel::setRegexDelim())
     *
     * @param string $delim Single character to use as preg delimiter
     *
     * @return boolean TRUE if the delimiter was set. FALSE otherwise
     */
    public function setDelim($delim)
    {
        return self::setRegexDelim($delim);
    }

    /**
     * Get regex delimiter
     *
     * (Shortcut alias for RegexTest_ChildModel::getRegexDelim())
     *
     * @return string
     */
    public function getDelim()
    {
        return self::$_delim_open;
    }
}
