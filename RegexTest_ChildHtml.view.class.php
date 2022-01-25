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
class RegexTest_ChildViewHtml extends RegexTest_ChildView
{
    private $_errors = array('modifiers' => '');

    /**
     * String of tabs to provide indenting for HTML tags
     *
     * @var string $tab
     */
    static protected $tab = "\t\t\t\t\t\t";

    static protected $resultStrings = array(
        'matched' => array(
            'len' => 300, 'len_sub' => 297
        ), 'sample' => array(
            'len' => 300, 'len_sub' => 297
        )
    );

    /**
     * Get the HTML for a given Regex pair
     *
     * @param integer              $index Index of pair to be returned
     * @param RegexTest_ChildModel $model Regex pair object
     *
     * @return string
     */
    public function getRegexFieldsetItem($index, RegexTest_ChildModel $model)
    {
        if (!is_int($index)) {
            // throw
            return '';
        }
        $a = $index;
        $b = ($index + 1);

        if ($a === 0) {
            $required = 'required="required" ';
        } else {
            $required = '';
        }


        if ($model->getMultiLine()) {
            $find = '<textarea name="regex['.$a.'][find]" '.
                              'id="find'.$a.'" '.
                              'class="find form-control" '.
                              'placeholder="Regex pattern '.$b.'" '.
                              $required.'/>' .
                        htmlspecialchars($model->getFind()) .
                    '</textarea>';
            $replace = '<textarea name="regex['.$a.'][replace]" '.
                                 'id="replace'.$a.'" '.
                                 'class="replace form-control" '.
                                 'placeholder="Replacement string '.$b.'" />' .
                            htmlspecialchars(
                                str_replace(
                                    array("\n", "\r", "\t"),
                                    array('\n', '\r', '\t'),
                                    $model->getReplace()
                                )
                            ) .
                       '</textarea>';
            $textareaClass = ' has-textarea';
        } else {
            $find = '<input type="text" '.
                           'name="regex['.$a.'][find]" '.
                           'id="find'.$a.'" '.
                           'value="'.htmlspecialchars($model->getFind()).'" '.
                           'class="find form-control" '.
                           'placeholder="Regex pattern '.$b.'" '.
                           $required . '/>';
            $replace = '<input type="text" '.
                            'name="regex['.$a.'][replace]" '.
                            'id="replace'.$a.'" '.
                            'value="'.htmlspecialchars(
                                str_replace(
                                    array("\n", "\r", "\t"),
                                    array('\n', '\r', '\t'),
                                    $model->getReplace()
                                )
                            ). '" '.
                            'class="replace form-control" '.
                            'placeholder="Replacement string '.$b.'" />';
            $textareaClass = '';
        }

        if (!$model->regexIsValid()) {
            $tmp = $model->getErrors();
            $error = "\n\t\t\t\t\t\t\t\t<p>".
                str_replace(
                    'preg_match() [function.preg-match.html]: ',
                    '',
                    $tmp['error_message']
                )."</p>\n\t\t\t\t\t\t\t\t".'<p class="error_high">'.
                    $tmp['error_highlight']."</p>\n";
            $error_class = ' bad-regex';
            unset($tmp);
        } else {
            $error = '';
            $error_class = '';
        }
        $checkbox_state = '';
        if ($model->getMultiLine() === true) {
            $checkbox_state = ' checked="checked"';
        }

        return '
						<li id="regexp'.$a.'" class="regexPair row'.$textareaClass.$error_class.'">
							<span class="frInputWrap col-sm-6 col-xs-12">
								<label for="find'.$a.'" class="hiding">'.
                                    'Find <span>'.$b.'</span>'.
                                '</label>
								' . $find . $error . '
							</span>

							<span class="frInputWrap col-sm-6 col-xs-12">
								<label for="replace'.$a.'" class="hiding">'.
                                    'Replace <span>'.$b.'</span>'.
                                '</label>
								' . $replace . '
							</span>

							<span class="col-xs-12 checkbox-check">
								<label for="modifiers'.$a.'">
								<input type="text" name="regex['.$a.'][modifiers]" '.
                                    'id="modifiers'.$a.'" '.
                                    'value="'.$model->getModifiers('original').'" '.
                                    'title="List of Regular Expression '.
                                           'pattern modifiers" '.
                                    'class="modifiers" '.
                                    'size="3" '.
                                    'pattern="[gimy]+|[imsxeADSXUJu]+" '.
                                    'placeholder="ig" '.
                                    'maxlength="12" />
									Pattern modifiers
								</label>
								<label for="makeTextarea'.$a.'">
									<input type="checkbox" '.
                                        'name="regex['.$a.'][makeTextarea]" '.
                                        'id="makeTextarea'.$a.'" '.
                                        'value="textarea" '.
                                        'title="Make Find '.$b.
                                        ' and Replace '.$b.' multi line" '.
                                        $checkbox_state.'/>
									Multi line
								</label>
							</span>
						</li>
';
    }


    /**
     * Format information generated regex::report() and any feedback
     * from adding/updating/deleting an archive
     *
     * @param RegexTest_ChildModel $model Object containing all info
     *                                    on regex processed
     *
     * @return string formatted contents of report
     *                (including archiver feedback)
     */
    public function formatReport(RegexTest_ChildModel $model)
    {
        $output = '';

        $report = $model->getReport();

        if (empty($report)) {
            return $output;
        }

        for ($a = 0; $a < count($report); $a += 1) {
            $output .= $this->formatReportItem($report[$a]);
        }
        return $output;
    }

    /**
     * Format information generated regex::report() and any feedback
     * from adding/updating/deleting an archive
     *
     * @param array $report Multi dimensional associative array
     *                      containing all info on regex processed
     *
     * @return string Formatted contents of report
     *                (including archiver feedback)
     */
    protected function formatReportItem($report)
    {
        $dud_class = '';
        $error_classes = array();
        $error_class = '';
        $output = '';

        if ($report['sample'] != '') {
            $output .= '
						<dt>Sample:</dt>
							<dd class="sample '.
                                $this->trimSimple($report['sample']).'">'.
                                $this->showSpace(
                                    htmlspecialchars($report['sample'])
                                ) .
                            '</dd>';
        }

        if ($report['time'] == -1) {
            $error_classes[] = 'time_error';
            $dud_class =  'dud';
        }

        $output .= "
						<dt>Time:</dt>
							<dd>{$report['time']}</dd>";

        if ($report['count'] == -1) {
            $error_classes[] = 'count_error';
            $dud_class =  'dud';
        }

        $output .= "
						<dt>Count:</dt>
							<dd>{$report['count']}</dd>";

        if ($report['error_message'] != 'No errors') {
            $dud_class = 'dud';
            $error_classes[] = 'RegexError';
            $output .= "
						<dt>Errors:</dt>
							<dd class=\"error_msg\">".
                                str_replace(
                                    'preg_match() [function.preg-match.html]: ',
                                    '',
                                    $report['error_message']
                                ).
                            '</dd>
							<dd class="error_high">'.
                                $report['error_highlight'].
                            '</dd>';
        }
        if (isset($report['output'])
            && is_array($report['output'])
            && !empty($report['output'])
        ) {
            $output .= '
						<dt>Matched:</dt>
							<dd class="matched">' . $this->formatPregResults($report['output']) . '
							</dd>';
        } else {
            $error_classes[] = 'no_matches';
            $output .= '
						<dt>Matched:</dt>
							<dd>Nothing was matched</dd>';
        }


        if ($dud_class != '') {
            array_unshift($error_classes, $dud_class);
        }
        $sep = '';
        for ($a = 0; $a < count($error_classes); $a += 1) {
            $error_class .= $sep . $error_classes[$a];
            $sep = ' ';
        }
        if ($error_class != '') {
            $error_class = ' class="' . $error_class . '"';
        }

        return '
			<li>
				<article' . $error_class . '>
					<dl class="table-def X4">
						<dt>Regex:</dt>
							<dd class="regex-pattern">' .
                                htmlspecialchars($report['regex']).
                            '</dd>'.'
' . $output . '
					</dl>
				</article>
			</li>
';
    }


    /**
     * Run through a multi dimensional array and convert array
     * values to items in hierarchical orderd lists
     *
     * @param array $input matched output from preg_match_all()
     *
     * @return string HTML code for the contents of the input array
     */
    protected function formatPregResults($input)
    {
        self::oneMoreTab();
        if (is_string($input[0]) && !empty($input[0])) {
            $output = "\n".self::$tab.'<span class="'.
                        $this->trimMatched($input[0]).'">'.
                        $this->showSpace(htmlspecialchars($input[0])).
                        '</span>';
            if (isset($input[1])) {
                unset($input[0]);
                $has_named = false;
                foreach ($input as $key => $value) {
                    if (is_string($key)) {
                        $has_named = true;
                        break;
                    }
                }
                $output .= "\n" . self::$tab;
                self::oneMoreTab();
                if ($has_named === true) {
                    $output .= '<ol class="matched-parts named">';
                    $named = false;
                    $a = 1;
                    foreach ($input as $key => $v0) {
                        if ($named === false) {
                            $output .= "\n" . self::$tab;
                            if (is_string($key)) {
                                $li_class = 'has-name';
                                $named = true;
                            } else {
                                $li_class = 'no-name';
                                $key = '&nbsp;';
                                $a += 1;
                            }
                            $output .= '<li class="'.$li_class.'">'.
                                            '<span class="name">'.$key.'</span> '.
                                            '<span class="value '.
                                                    $this->trimMatched($v0).
                                            '">'.
                                                $this->showSpace(
                                                    htmlspecialchars($v0), 'matched'
                                                ).
                                            '</span>'.
                                        '</li>';
                        } else {
                            $named = false;
                            $a += 1;
                        }
                    }
                } else {
                    $output .= '<ol class="matched-parts">';
                    foreach ($input as $v0) {
                        $output .= "\n".self::$tab.'<li class="'.
                                        $this->trimMatched($v0).
                                    '">'.
                                        $this->showSpace(
                                            htmlspecialchars($v0), 'matched'
                                        ) .
                                    '</li>';
                        // $output .= "\n".self::$tab.'<li>'.
                        //                $this->showSpace($v0).
                        //            '</li>';
                    }
                }
                self::oneLessTab();
                $output .= "\n" . self::$tab . '</ol>';
            }
        } else {
            $output = "\n" . self::$tab . '<ol>';
            self::oneMoreTab();
            foreach ($input as $v0) {
                if (is_array($v0) && !empty($v0)) {
                    $output .= "\n".self::$tab.'<li>'.
                                    $this->formatPregResults($v0).
                               "\n".self::$tab.'</li>';
                }
            }
            self::oneLessTab();
            $output .= "\n" . self::$tab . '</ol>';
        }

        self::oneLessTab();

        return $output;
    }


    /**
     * Convert white space characters into visible strings

     * @param string $input text whose white spaces are to be converted
     * @param string $type  Result type
     *
     * @return string text with whitespaces converted to visible strings
     */
    protected function showSpace($input, $type = 'sample')
    {
        if (!is_string($type) || !isset(self::$resultStrings[$type])) {
            $type = 'sample';
        }
        $sampleLen = self::$resultStrings[$type]['len'];
        $sampleLen_sub = self::$resultStrings[$type]['len_sub'];
        if ($sampleLen > 0 && strlen($input) > $sampleLen) {
            $input = substr_replace($input, '', $sampleLen_sub) . '...';
        }
        $find = array(' ', "\n", "\t");
        $replace = array(
            ' <span class="space">[SPACE]</span> ',
            ' <span class="space">[NEW_LINE]</span> ',
            ' <span class="space">[TAB]</span> '
        );
        return str_replace($find, $replace, $input);
    }

    /**
     * Set the number of tab characters to use in output
     *
     * @param integer $tcount Number of tab characters to use
     *
     * @return void
     */
    static public function setTab($tcount)
    {
        if (is_numeric($tcount)) {
            self::$tab = '';
            for ($a = 0; $a < $tcount; ++$a) {
                self::$tab .= "\t";
            }
        }
    }

    /**
     * Remove the last tab character
     *
     * @return void
     */
    static protected function oneLessTab()
    {
        self::$tab = substr_replace(self::$tab, '', 0, 1);
    }

    /**
     * Add an extra tab character
     *
     * @return void
     */
    static protected function oneMoreTab()
    {
        self::$tab .= "\t";
    }

    /**
     * Trim sample string if longer than the maximum sample length
     * allowed
     *
     * @param string $input Sample string
     *
     * @return string "truncated" if the string was longer than allowed.
     *                "whole" if string was within the maximum allowable length
     */
    protected function trimSimple(&$input)
    {
        if (strlen($input) > self::$sampleLen) {
            $input = substr($input, 0, (self::$sampleLen - 3)) . '...';
            return 'truncated';
        }
        return 'whole';
    }

    /**
     * Trim matched string if longer than the maximum sample length
     * allowed
     *
     * @param string $input Matched string
     *
     * @return string "truncated" if the string was longer than allowed.
     *                "whole" if string was within the maximum allowable length
     */
    protected function trimMatched(&$input)
    {
        if (strlen($input) > self::$matchedLen) {
            $input = substr($input, 0, (self::$matchedLen - 3)) . '...';
            return 'truncated';
        }
        return 'whole';
    }
}
