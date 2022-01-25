<?php
/**
 * This file contains two view classes for rendering out of a regex
 * test request
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
 * Check Parent View HTML prepares output for rendering in HTML
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class RegexTest_ParentViewHtml extends RegexTest_ParentView
{
    protected $result_wrapper_class = 'single-sample';

    public function getOutput()
    {
        $results = '';
        $pairs = '';
        $extra_tabs = '';
        $output = '';
        $regexes = $this->model->getProp('regexes');
        $sampleLen_cls = '';
        $matchedLen_cls = '';
        $regexDelim_cls = '';
        $active = '';

        $show_output = false;

        if ($this->model->getProp('dummy')) {
            $pairs .= $this->child_view->getRegexFieldsetItem(0, $regexes[0]);
            $active = 'sample';
        } else {
            if ($this->model->getProp('testOnly') === false
                && $this->model->getProp('output_is_different')
            ) {
                $show_output = true;
            }

            for ($a = 0; $a < count($regexes); $a += 1) {
                $pairs .= $this->child_view->getRegexFieldsetItem(
                    $a, $regexes[$a]
                );
                $results .= $this->getOutputResults(
                    $this->child_view->formatReport($regexes[$a])
                );
            }
            if ($results !== '') {
                $active = '';
                if ($show_output !== true) {
                    $active_cls = ' active in';
                    $active_cls_ = ' class="active"';
                } else {
                    $active_cls = '';
                    $active_cls_ = '';
                }

                $extra_tabs .= '
                    <li'.$active_cls_.'>'.
                        '<a href="#results" data-toggle="tab" id="results-tab">'.
                            'Results'.
                        '</a>'.
                    '</li>';
                $results = '
                <section id="results" class="tab-pane fade'.$active_cls.' results">
                    <legend>Results</legend>
                    <ol class="'.$this->result_wrapper_class.'">'.$results.'
                    </ol>
                </section>
';
            } else {
                $active = 'regex';
            }

            if ($show_output) {
                $active = '';
                $extra_tabs .= '
                <li class="active">'.
                    '<a href="#output" data-toggle="tab" id="output-tab">Output</a>'.
                '</li>';
                $output = '
                <fieldset id="output" class="tab-pane fade active in">
                    <legend>Replacement</legend>
                    <textarea id="replace" name="replace" readonly="readonly">'.
                        htmlspecialchars($this->model->getProp('output')).
                    '</textarea>
                </fieldset>
';
            }
        }

        if ($this->model->getProp('ws_trim_after')) {
            $wsTrimAfter = ' checked="checked"';
            $wsTrimBefore = '';
        } else {
            $wsTrimAfter = '';
            $wsTrimBefore = ' checked="checked"';
        }

        if ($this->model->getProp('ws_trim')) {
            $wsTrim_cb = ' checked="checked"';
            $wsTrim_label_cls = '';
        } else {
            $wsTrim_cb = '';
            $wsTrimBefore .= ' disabled="disabled"';
            $wsTrimAfter .= ' disabled="disabled"';
            $wsTrim_label_cls = 'disabled';
        }

        if ($this->model->getProp('split_sample')) {
            $splitSample_cb = ' checked="checked"';
            $splitDelim_disabled = '';
            $splitDelim_label_cls = '';
        } else {
            $splitDelim_disabled = ' disabled="disabled"';
            $splitSample_cb = '';
            $splitDelim_label_cls = 'disabled';
        }

        $splitDelim = $this->model->getProp('split_delim');

        switch ($active) {
        case 'sample':
            $active_sample = ' active in';
            $active_sample_cls = ' class="active"';
            $active_regex = '';
            $active_regex_cls = '';
            break;

        case 'regex':
            $active_sample = '';
            $active_sample_cls = '';
            $active_regex = ' active in';
            $active_regex_cls = ' class="active"';
            break;

        default:
            $active_sample = '';
            $active_sample_cls = '';
            $active_regex = '';
            $active_regex_cls = '';
        }

        if (!$this->model->getProp('sampleLenOk')) {
            $sampleLen_cls = ' class="error"';
        }

        if (!$this->model->getProp('matchedLenOk')) {
            $matchedLen_cls = ' class="error"';
        }
        if (!$this->model->getProp('regexDelimOk')) {
            $regexDelim_cls = ' class="error"';
        }

        $find = array(
             '{{REQUEST_URI}}'           //  [0] $this->model->getProp('requestUri')
            ,'{{PAIRS}}'                 //  [1] $pairs
            ,'{{RESULTS}}'               //  [2] $results
            ,'{{EXTRA_TABS}}'            //  [3] $extra_tabs
            ,'{{SAMPLE}}'                //  [4] $this->model->getProp('sample')
            ,'{{OUTPUT}}'                //  [5] $output
            ,'{{ACTIVE_SAMPLE}}'         //  [6]$active_sample
            ,'{{ACTIVE_SAMPLE_CLS}}'     //  [7] $active_sample_cls
            ,'{{ACTIVE_REGEX}}'          //  [8] $active_regex
            ,'{{ACTIVE_REGEX_CLS}}'      //  [9] $active_regex_cls
            ,'{{SPLIT_SAMPLE_CB}}'       // [10]$splitSample_cb
            ,'{{SPLIT_DELIM}}'           // [11] $splitDelim
            ,'{{SPLIT_DELIM_LABEL_CLS}}' // [12] $splitDelim_disabled
            ,'{{SPLIT_DELIM_DISABLED}}'  // [13] $splitDelim_label_cls
            ,'{{WS_TRIM_CB}}'            // [14] $wsTrim_cb
            ,'{{WS_TRIM_LABEL_CLS}}'     // [15] $wsTrim_true_label_cls
            ,'{{WS_TRIM_BEFORE}}'        // [16] $wsTrimBefore
            ,'{{WS_TRIM_AFTER}}'         // [17] $wsTrimAfter
            ,'{{SAMPLE_LEN}}'            // [18] $this->model->get_static_prop('sample_len')
            ,'{{SAMPLE_LEN_CLS}}'        // [19] $sampleLen_cls
            ,'{{MATCHED_LEN}}'           // [20] $this->model->get_static_prop('matched_len')
            ,'{{MATCHED_LEN_CLS}}'       // [21] $matchedLen_cls
            ,'{{REGEX_DELIM}}'           // [22] $this->model->get_static_prop('regex_delim')
            ,'{{REGEX_DELIM_CLS}}'       // [23] $regexDelim_cls
        );

        $replace = array(
             $this->model->getProp('requestUri') // [0] 'requestUri'
            ,$pairs                      //  [1] 'PAIRS'
            ,$results                    //  [2] 'RESULTS'
            ,$extra_tabs                 //  [3] 'EXTRA_TABS'
            ,htmlspecialchars($this->model->getProp('sample')) // [4] 'SAMPLE'
            ,$output                     //  [5] OUTPUT
            ,$active_sample              //  [6] ACTIVE_SAMPLE
            ,$active_sample_cls          //  [7] ACTIVE_SAMPLE_CLS
            ,$active_regex               //  [8] ACTIVE_REGEX
            ,$active_regex_cls           //  [9] ACTIVE_REGEX_CLS
            ,$splitSample_cb             // [10] SPLIT_SAMPLE_CB
            ,$splitDelim                 // [11] SPLIT_DELIM
            ,$splitDelim_label_cls       // [12] SPLIT_DELIM_DISABLED
            ,$splitDelim_disabled        // [13] SPLIT_DELIM_LABEL_CLS
            ,$wsTrim_cb                  // [14] WS_TRIM_CB
            ,$wsTrim_label_cls           // [15] WS_TRIM_TRUE_LABEL_CLS
            ,$wsTrimBefore               // [16] WS_TRIM_TRUE
            ,$wsTrimAfter                // [17] WS_TRIM_FALSE
            ,$this->model->getProp('sampleLen') // [18] SAMPLE_LEN
            ,$sampleLen_cls              // [19] SAMPLE_LEN_CLS
            ,$this->model->getProp('matchedLen') // [19] MATCHED_LEN
            ,$matchedLen_cls             // [21] MATCHED_LEN_CLS
            ,$this->model->getProp('regexDelim') // [20] DELIM_CLOSE
            ,$regexDelim_cls             // [23] REGEX_DELIM_CLS
        );

        // debug('$wsTrimBefore', $wsTrimBefore, $find, $replace);
        return str_replace(
            $find,
            $replace,
            file_get_contents('RegexTest_template.html')
        );
    }

    protected function getOutputResults($input)
    {
        return $input;
    }
}

class RegexTest_ParentViewHtmlMulti extends RegexTest_ParentViewHtml
{

    protected $result_wrapper_class = 'multi-sample';

    protected function getOutputResults($input)
    {
        return '
                <li>
                    <ol>'.$input.'
                    </ol>
                </li>';
    }
}
