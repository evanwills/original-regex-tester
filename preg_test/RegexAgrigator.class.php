<?php
/**
 * This file contains single class for agrigating regex pairs &
 * sample strings
 *
 * PHP Version 5.4, 7.x, 8.0
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */

if (!class_exists('regex')) {
    require 'regex.class.php';
}

/**
 * RegexAgrigator provides additional functionality for regex
 * classes to allow multiple regular expressions, multiple
 * replacment values and multiple sample strings to all be
 * tested/processed in a single pass
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexAgrigator
{
    /**
     * List of regex objects
     *
     * @var array $regexes
     */
    protected $regexes = array();

    /**
     * List of sample strings to apply regexes to
     *
     * @var array $samples
     */
    protected $samples = array();

    /**
     * String of tabs to provide indenting for HTML tags
     *
     * @var string $tab
     */
    protected $tab = "\t\t\t\t\t\t";

    /**
     * Whether or not any of the supplied regexes had an error
     *
     * @var boolean $dud
     */
    protected $dud = false;

    /**
     * Constructor for regex
     *
     * @param array $find    list of regexes to be tested
     * @param array $replace list of replacement values/patterns to be
     *                       used
     * @param array $samples list of sample strings to test the regexes
     *                       against
     *
     * @return void
     */
    public function __construct($find, $replace, $samples)
    {
        $input = array('find', 'replace', 'samples');

        foreach ($input as $value) {
            if (is_string($$value)) {
                $this->$value = array($$value);
            } elseif (!is_array($$value)) {
                throw new Exception(
                    'RegexAgrigator constructor expects '.$value.
                    ' to be an array. '.gettype($$value).' given'
                );
            } else {
                $this->$value = $$value;
            }
        }
        if (!empty($replace)) {
            $input = '';
        } else {
            $input = false;
        }

        foreach ($find as $key => $value) {
            if (!isset($replace[$key])) {
                $replacement = $input;
            } else {
                $replacement = $replace[$key];
            }
            $this->regexes[] = regex::process($value, $replacement);
        }
        $this->samples = $samples;
    }

    /**
     * Apply all regexes to a given string.
     *
     * Regexes are applied in order of inclusion. What was matched is
     * shown along with how long it took, how many matches were made,
     * what if any error messages were generated and the regex with
     * errors highligted
     *
     * @param array $samples Sample text regexes are to be tested
     *                       against
     *
     * @return array list of results for each regex supplied
     */
    public function report(array $samples)
    {
        $output = array();
        foreach ($samples as $sample) {
            $sub_output = array();
            foreach ($this->regexes as $regex) {
                $sub_output[] = $regex->report($sample);
                $sample = $regex->getOutput($sample);
                $c = (count($sub_output) - 1);
                if ($sub_output[$c]['type'] == 'error') {
                    $this->dud = true;
                }
            }
            $output[] = $sub_output;
        }
        return $output;
    }

    /**
     * Apply regex find and replace to the sample supplied.
     *
     * @param array $samples Sample text regexes are to be tested
     *                       against
     *
     * @return array list of results for each regex supplied
     */
    public function processSamples(array $samples)
    {
        $output = array();
        foreach ($samples as $sample) {
            foreach ($this->regexes as $regex) {
                $sample = $regex->getOutput($sample);
            }
            $output[] = self::splitClean($sample);
        }
        return $output;
    }

    /**
     * Whether or not the regex is broken
     *
     * @return boolean
     */
    public function isDud()
    {
        return $this->dud;
    }

    /**
     * Make it easy to prepare inputs for RegexAgrigator
     *
     * @param string  $input String that may or may not need to be
     *                       split
     * @param boolean $multi whether or not to split the string
     * @param string  $split Characters used to split the input
     *                       string if required
     *
     * @return array an array containing at least one item
     */
    public static function regexExplode($input, $multi = false, $split = "\n")
    {
        if ($multi === false) {
            return array($input);
        } else {
            $throughput = explode(self::splitClean($split), $input);
            for ($a = 0; $a < count($throughput); $a += 1) {
                $throughput[$a] = trim($throughput[$a]);
            }
            return $throughput;
        }
    }

    /**
     * Make it easy to return processed inputs to strings
     *
     * @param array   $input List of strings that need to be
     *                       concatinated
     * @param boolean $multi Whether or not to split the string
     * @param string  $split Characters used to split the input
     *                       string if required
     *
     * @return string A single string containing all the items in the
     *                array delimited by $split.
     */
    public static function regexImplode($input, $multi = false, $split = "\n")
    {
        if (is_array($input)) {
            return implode(self::splitClean($split), $input);
        } elseif (is_string($input)) {
            return $input;
        }
        return '';
    }

    /**
     * Convert split escaped white space character sequences to their
     * normal white space character equivalents
     *
     * @param string $input String to be converted
     *
     * @return string Converted string
     */
    protected static function splitClean($input)
    {
        return str_replace(
            array('\r\n', '\n', '\r', '\t'),
            array("\n", "\n", "\r", "\t"),
            $input
        );
    }
}
