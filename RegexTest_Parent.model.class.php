<?php
/**
 * This file contains a single model classes for all the data
 * submitted in a user's request
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
 * Get all the data from a user's request
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexTest_ParentModel
{
    protected $parent_view_class = 'RegexTest_ParentViewHtml';
    protected $child_view_class = 'RegexTest_ChildViewHtml';
    protected $sample = '';
    protected $wsTrim = false;
    protected $wsActionAfter = true;
    protected $splitSample = false;
    protected $splitDelim = '\n';
    protected $regexes = array();
    protected $output = '';
    protected $output_is_different = false;
    protected $test_only = true;
    protected $dummy = true;
    protected $request_url = '';
    protected $sampleLen = 300;
    protected $sampleLenOk = true;
    protected $matchedLen = 300;
    protected $matchedLen_ok = true;
    protected $regexDelim = '`';
    protected $regexDelimOk = true;


    /**
     * Constructor
     *
     * Note: this constructor pulls all of its data from POST values
     *       in the user request
     */
    public function __construct()
    {

        if (isset($_POST)) {
            $this->dummy = false;
            $this->sample = isset($_POST['sample'])
                ? $_POST['sample']
                : $this->sample;

            $this->wsTrim = isset($_POST['ws_trim'])
                ? true
                : false;

            if (isset($_POST['ws_action']) && $_POST['ws_action'] == 'before') {
                $this->wsActionAfter = false;
            }

            if ($this->wsTrim === true && $this->wsActionAfter === false) {
                $this->sample = trim($this->sample);
            }

            $this->splitSample = isset($_POST['split_sample'])
                ? true
                : false;

            $this->splitDelim = isset($_POST['split_delim'])
                ? $_POST['split_delim']
                : $this->splitDelim;

            $this->regexDelim = isset($_POST['regex_delim'])
                ? $_POST['regex_delim']
                : $this->regexDelim;


            if (!RegexTest_ChildModel::setRegexDelim($this->regexDelim)) {
                $this->regexDelim = RegexTest_ChildModel::getRegexDelim();
                $this->regexDelimOk = false;
            }

            $this->sampleLen = isset($_POST['sample_len'])
                ? $_POST['sample_len']
                : $this->sampleLen;

            $this->matchedLen = isset($_POST['matched_len'])
                ? $_POST['matched_len']
                : $this->matchedLen;


            if (isset($_POST['regex'])
                && is_array($_POST['regex'])
                && !empty($_POST['regex'])
            ) {
                $tmp = $this->sample;
                if ($this->wsTrim === true && $this->wsActionAfter === false) {
                    $tmp = trim($tmp);
                }
                $output = $tmp_output = $tmp;
                unset($tmp);

                if ($this->splitSample === true) {
                    $imploder = $this->splitDelim;
                    switch ($imploder) {
                    case '\n':
                        $imploder = "\n";
                        break;
                    case '\r':
                        $imploder = "\r";
                        break;
                    case '\r\n':
                        $imploder = "\r\n";
                        break;
                    case '\t':
                        $imploder = "\t";
                        break;
                    case '\f':
                        $imploder = "\f";
                        break;
                    case '\e':
                        $imploder = "\e";
                        break;
                    }
                    $output = explode($imploder, $output);
                } else {
                    $output = array($output);
                }

                if ($this->wsTrim === true && $this->wsActionAfter === false) {
                    for ($a = 0; $a < count($output); $a += 1) {
                        $output[$a] = trim($output[$a]);
                    }
                }

                for ($a = 0; $a < count($_POST['regex']); $a += 1) {
                    $find = isset($_POST['regex'][$a]['find'])
                        ? $_POST['regex'][$a]['find']
                        : '';
                    $replace =  preg_replace_callback(
                        '`(?<!\\\\)\\\\([rnt])`',
                        function ($matches) {
                            switch ($matches[1]) {
                            case 'n':
                                return "\n";
                                break;
                            case 'r':
                                return "\r";
                                break;
                            case 't':
                                return "\t";
                                break;
                            }
                        },
                        isset($_POST['regex'][$a]['replace'])
                            ? $_POST['regex'][$a]['replace']
                            : ''
                    );
                    $modifiers = isset($_POST['regex'][$a]['modifiers'])
                        ? $_POST['regex'][$a]['modifiers']
                        : '';
                    $makeTextarea = isset($_POST['regex'][$a]['makeTextarea'])
                        ? true
                        : false;
                    $tmp = new RegexTest_ChildModel(
                        $find,
                        $replace,
                        $modifiers,
                        $makeTextarea
                    );
                    $this->regexes[] = $tmp;

                    for ($b = 0; $b < count($output); $b += 1) {
                        $output[$b] = $tmp->process($output[$b]);
                    }
                }
                if ($this->splitSample === true) {
                    $output = implode($imploder, $output);
                } else {
                    $output = $output[0];
                }
                if ($tmp_output !== $output) {
                    if ($this->wsTrim === true && $this->wsActionAfter === true) {
                        $output = trim($output);
                    }
                    $this->output = $output;
                    $this->output_is_different = true;
                }
            } else {
                $this->regexes[] = new RegexTest_ChildModel('', '', '', false);
            }
            if (isset($_POST['submit_replace'])) {
                $this->test_only = false;
            }
            $this->request_uri = $_SERVER['REQUEST_URI'];
        }
    }

    /**
     * Convert white space escape sequences into their normal which
     * space characters
     *
     * @param string $input String to be converted
     *
     * @return string
     */
    private function _fixWhiteSpace($input)
    {
        return preg_replace_callback(
            '`(?<!\\\\)\\\\([rnt])`',
            function ($matches) {
                switch ($matches[1]) {
                case 'n':
                    return "\n";
                    break;
                case 'r':
                    return "\r";
                    break;
                case 't':
                    return "\t";
                    break;
                }
            },
            $input
        );
    }

    /**
     * Get the value from a property
     *
     * @param string $prop_name Name of object property
     *
     * @return mixed
     */
    public function getProp($prop_name)
    {
        if (is_string($prop_name) && $prop_name != ''
            && property_exists($this, $prop_name)
        ) {
            return $this->$prop_name;
        } else {
            // throw
        }
    }

    /**
     * Set whether or not the supplied length is bad
     *
     * @param string $len Type of length being set
     *
     * @return void
     */
    public function setLenNotOk($len = 'sample')
    {
        if ($len == 'sample') {
            $this->sampleLenOk = false;
        }
        if ($len == 'matched') {
            $this->matchedLen_ok = false;
        }
    }
}
