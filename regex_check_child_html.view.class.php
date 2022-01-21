<?php

class regex_check_child_view_html extends regex_check_child_view
{
	private $errors = array('modifiers' => '');

/**
 * @var string $tab string of tabs to provide indenting for HTML tags
 */
	static protected $tab = "\t\t\t\t\t\t";

	static protected $result_strings = array(
		 'matched' => array(
			 'len' => 300
			,'len_sub' => 297
		 )
		,'sample' => array(
			 'len' => 300
			,'len_sub' => 297
		 )
	);



	public function get_regex_fieldset_item( $index , regex_check_child_model $model )
	{
		if( !is_int($index) )
		{
			// throw
			return '';
		}
		$a = $index;
		$b = ( $index + 1 );

		if( $a === 0 )
		{
			$required = 'required="required" ';
		}
		else
		{
			$required = '';
		}


		if( $model->get_multiline() )
		{
			$find = '<textarea name="regex['.$a.'][find]" id="find'.$a.'" class="find form-control" placeholder="Regex pattern '.$b.'" '.$required.'/>'.htmlspecialchars($model->get_find()).'</textarea>';
			$replace = '<textarea name="regex['.$a.'][replace]" id="replace'.$a.'" class="replace form-control" placeholder="Replacement string '.$b.'" />'.htmlspecialchars(str_replace(array("\n","\r","\t"),array('\n','\r','\t'),$model->get_replace())).'</textarea>';
			$textareaClass = ' has-textarea';
		}
		else
		{
			$find = '<input type="text" name="regex['.$a.'][find]" id="find'.$a.'" value="'.htmlspecialchars($model->get_find()).'" class="find form-control" placeholder="Regex pattern '.$b.'" '.$required.'/>';
			$replace = '<input type="text" name="regex['.$a.'][replace]" id="replace'.$a.'" value="'.htmlspecialchars(str_replace(array("\n","\r","\t"),array('\n','\r','\t'),$model->get_replace())).'" class="replace form-control" placeholder="Replacement string '.$b.'" />';
			$textareaClass = '';
		}

		if( !$model->is_regex_valid() )
		{
			$tmp = $model->get_errors();
			$error = "\n\t\t\t\t\t\t\t\t<p>".str_replace('preg_match() [function.preg-match.html]: ','',$tmp['error_message'])."</p>\n\t\t\t\t\t\t\t\t<p class=\"error_high\">{$tmp['error_highlight']}</p>\n";
			$error_class = ' bad-regex';
			unset($tmp);
		}
		else
		{
			$error = '';
			$error_class = '';
		}
		$checkbox_state = '';
		if( $model->get_multiline() === true )
		{
			$checkbox_state = ' checked="checked"';
		}

		return '
						<li id="regexp'.$a.'" class="regexPair row'.$textareaClass.$error_class.'">
							<span class="frInputWrap col-sm-6 col-xs-12">
								<label for="find'.$a.'" class="hiding">Find <span>'.$b.'</span></label>
								'.$find.$error.'
							</span>

							<span class="frInputWrap col-sm-6 col-xs-12">
								<label for="replace'.$a.'" class="hiding">Replace <span>'.$b.'</span></label>
								'.$replace.'
							</span>

							<span class="col-xs-12 checkbox-check">
								<label for="modifiers'.$a.'">
								<input type="text" name="regex['.$a.'][modifiers]" id="modifiers'.$a.'" value="'.$model->get_modifiers('original').'" title="List of Regular Expression pattern modifiers" class="modifiers" size="3" pattern="[gimy]+|[imsxeADSXUJu]+" placeholder="ig" maxlength="12" />
									Pattern modifiers
								</label>
								<label for="makeTextarea'.$a.'">
									<input type="checkbox" name="regex['.$a.'][makeTextarea]" id="makeTextarea'.$a.'" value="textarea" title="Make Find '.$b.' and Replace '.$b.' multi line" '.$checkbox_state.'/>
									Multi line
								</label>
							</span>
						</li>
';
	}


/**
 * @method format_report() formats information generated regex::report()
 *	   and any feedback from adding/updating/deleting an archive
 *
 * @param array $report multi dimensional associative array containing
 *	  all info on regex processed
 *
 * @return string formatted contents of report (including archiver
 *	   feedback)
 */
	public function format_report( regex_check_child_model $model )
	{
		$output = '';

		$report = $model->get_report();

		if( empty($report) )
		{
			return $output;
		}

		for( $a = 0 ; $a < count($report) ; $a += 1 )
		{
			$output .= $this->format_report_item( $report[$a] );
		}
		return $output;
	}

/**
 * @method format_report() formats information generated regex::report()
 *	   and any feedback from adding/updating/deleting an archive
 *
 * @param array $report multi dimensional associative array containing
 *	  all info on regex processed
 *
 * @return string formatted contents of report (including archiver
 *	   feedback)
 */
	protected function format_report_item( $report )
	{
		$dud_class = '';
		$error_classes = array();
		$error_class = '';
		$output = '';

		if( $report['sample'] != '' )
		{
			$output .= '
						<dt>Sample:</dt>
							<dd class="sample '.$this->trim_sample_class($report['sample']).'">'.$this->show_space(htmlspecialchars($report['sample'])).'</dd>';
		}

		if( $report['time'] == -1 )
		{
			$error_classes[] = 'time_error';
			$dud_class =  'dud';
		}

		$output .= "
						<dt>Time:</dt>
							<dd>{$report['time']}</dd>";

		if($report['count'] == -1 )
		{
			$error_classes[] = 'count_error';
			$dud_class =  'dud';
		}

		$output .= "
						<dt>Count:</dt>
							<dd>{$report['count']}</dd>";

		if( $report['error_message'] != 'No errors' )
		{
			$dud_class = 'dud';
			$error_classes[] = 'regex_error';
			$output .= "
						<dt>Errors:</dt>
							<dd class=\"error_msg\">".str_replace('preg_match() [function.preg-match.html]: ','',$report['error_message'])."</dd>
							<dd class=\"error_high\">{$report['error_highlight']}</dd>";
		}
		if( isset($report['output']) && is_array($report['output']) && !empty($report['output']) )
		{
			$output .= '
						<dt>Matched:</dt>
							<dd class="matched">'.$this->format_preg_results($report['output']).'
							</dd>';
		}
		else
		{
			$error_classes[] = 'no_matches';
			$output .= '
						<dt>Matched:</dt>
							<dd>Nothing was matched</dd>';
		}


		if( $dud_class != '' )
		{
			array_unshift( $error_classes , $dud_class );
		}
		$sep = '';
		for( $a = 0 ; $a < count($error_classes) ; $a += 1 )
		{
			$error_class .= $sep.$error_classes[$a];
			$sep = ' ';
		}
		if( $error_class != '' )
		{
			$error_class = ' class="'.$error_class.'"';
		}

		return '
			<li>
				<article'.$error_class.'>
					<dl class="table-def X4">
						<dt>Regex:</dt>
							<dd class="regex-pattern">'.htmlspecialchars($report['regex']).'</dd>'.'
'.$output.'
					</dl>
				</article>
			</li>
';
	}


/**
 * @method format_preg_results() runs through a multi dimensional
 * array and converts array values to items in hierarchical orderd
 * lists
 *
 * @param array $input matched output from preg_match_all()
 * @return string HTML code for the contents of the input array
 */
	protected function format_preg_results($input)
	{
		self::one_more_tab();
		if( is_string($input[0]) && !empty($input[0]) )
		{
			$output = "\n".self::$tab.'<span class="'.$this->trim_matched_class($input[0]).'">'.$this->show_space(htmlspecialchars($input[0])).'</span>';
			if( isset($input[1]) )
			{
				unset($input[0]);
				$has_named = false;
				foreach( $input as $key => $value )
				{
					if( is_string($key) )
					{
						$has_named = true;
						break;
					}
				}
				$output .= "\n".self::$tab;
				self::one_more_tab();
				if( $has_named === true )
				{
					$output .= '<ol class="matched-parts named">';
					$named = false;
					$a = 1;
					foreach( $input as $key => $v0 )
					{
						if( $named === false )
						{
							$output .= "\n".self::$tab;
							if( is_string($key) )
							{
								$li_class = 'has-name';
								$named = true;
							}
							else
							{
								$li_class = 'no-name';
								$key = '&nbsp;';
								$a += 1;
							}
							$output .= '<li class="'.$li_class.'"><span class="name">'.$key.'</span> <span class="value '.$this->trim_matched_class($v0).'">'.$this->show_space(htmlspecialchars($v0),'matched').'</span></li>';
						}
						else
						{
							$named = false;
							$a += 1;
						}

					}
				}
				else
				{
					$output .= '<ol class="matched-parts">';
					foreach( $input as $v0 )
					{
						$output .= "\n".self::$tab.'<li class="'.$this->trim_matched_class($v0).'">'.$this->show_space(htmlspecialchars($v0),'matched').'</li>';
//						$output .= "\n".self::$tab.'<li>'.$this->show_space($v0).'</li>';
					}
				}
				self::one_less_tab();
				$output .= "\n".self::$tab.'</ol>';
			}
		}
		else
		{
			$output = "\n".self::$tab.'<ol>';
			self::one_more_tab();
			foreach( $input as $v0 )
			{
				if( is_array($v0) && !empty($v0) )
				{
					$output .= "\n".self::$tab.'<li>'.$this->format_preg_results($v0)."\n".self::$tab.'</li>';
				}
			}
			self::one_less_tab();
			$output .= "\n".self::$tab.'</ol>';
		}

		self::one_less_tab();

		return $output;
	}


/**
 * @method show_space() converts white space characters into visible
 * strings
 * @param string $input text whose white spaces are to be converted
 * @return string text with whitespaces converted to visible strings
 */
	protected function show_space( $input , $type = 'sample' )
	{
		if( !is_string($type) || !isset(self::$result_strings[$type]) )
		{
			$type = 'sample';
		}
		$sample_len = self::$result_strings[$type]['len'];
		$sample_len_sub = self::$result_strings[$type]['len_sub'];
		if( $sample_len > 0 && strlen($input) > $sample_len )
		{
			$input = substr_replace( $input , '' , $sample_len_sub ).'...';
		}
		$find = array(' ' , "\n" , "\t");
		$replace = array(' <span class="space">[SPACE]</span> ' , ' <span class="space">[NEW_LINE]</span> ' , ' <span class="space">[TAB]</span> ' );
		return str_replace($find , $replace , $input );
	}

	static public function set_tab( $tcount )
	{
		if( is_numeric( $tcount ) )
		{
			self::$tab = '';
			for( $a = 0 ; $a < $tcount ; ++$a )
			{
				self::$tab .= "\t";
			}
		}
	}

	static protected function one_less_tab()
	{
		self::$tab = substr_replace(self::$tab,'',0,1);
	}

	static protected function one_more_tab()
	{
		self::$tab .= "\t";
	}



	protected function trim_sample_class( &$input )
	{
		if( strlen($input) > self::$sample_len )
		{
			$input = substr( $input , 0 , ( self::$sample_len - 3 ) ).'...';
			return 'truncated';
		}
		return 'whole';
	}

	protected function trim_matched_class( &$input )
	{
		if( strlen($input) > self::$matched_len )
		{
			$input = substr( $input , 0 , ( self::$matched_len - 3 ) ).'...';
			return 'truncated';
		}
		return 'whole';
	}

}
