<?php

class regex_check_parent_model
{
	protected $parent_view_class = 'regex_check_parent_view_html';
	protected $child_view_class = 'regex_check_child_view_html';
	protected $sample = '';
	protected $ws_trim = false;
	protected $ws_action_after = true;
	protected $split_sample = false;
	protected $split_delim = '\n';
	protected $regexes = array();
	protected $output = '';
	protected $output_is_different = false;
	protected $test_only = true;
	protected $dummy = true;
	protected $request_url = '';
	protected $sample_len = 300;
	protected $sample_len_ok = true;
	protected $matched_len = 300;
	protected $matched_len_ok = true;
	protected $regex_delim = '`';
	protected $regex_delim_ok = true;


	public function __construct()
	{

		if( isset($_POST) )
		{
			$this->dummy = false;
			$this->sample = isset($_POST['sample'])?$_POST['sample']:$this->sample;
			$this->ws_trim = isset($_POST['ws_trim'])?true:false;
			if( isset($_POST['ws_action']) && $_POST['ws_action'] == 'before' )
			{
				$this->ws_action_after = false;
			}
			if( $this->ws_trim === true && $this->ws_action_after === false )
			{
				$this->sample = trim($this->sample);
			}

			$this->split_sample = isset($_POST['split_sample'])?true:false;
			$this->split_delim = isset($_POST['split_delim'])?$_POST['split_delim']:$this->split_delim;
			$this->regex_delim = isset($_POST['regex_delim'])?$_POST['regex_delim']:$this->regex_delim;

			if( !regex_check_child_model::set_regex_delim($this->regex_delim) )
			{
				$this->regex_delim = regex_check_child_model::get_regex_delim();
				$this->regex_delim_ok = false;
			}

			$this->sample_len = isset($_POST['sample_len'])?$_POST['sample_len']:$this->sample_len;
			$this->matched_len = isset($_POST['matched_len'])?$_POST['matched_len']:$this->matched_len;


			if( isset( $_POST['regex'] ) && is_array($_POST['regex']) && !empty($_POST['regex']) )
			{
				if( $this->ws_trim === true && $this->ws_action_after === false )
				{
					$output = $tmp_output = trim($this->sample);
				}
				else
				{
					$output = $tmp_output = $this->sample;
				}

				if( $this->split_sample === true )
				{
					$imploder = $this->split_delim;
					switch($imploder)
					{
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
					$output = explode( $imploder , $output );
				}
				else
				{
					$output = array($output);
				}
				for( $a = 0 ; $a < count($_POST['regex']) ; $a += 1 )
				{
					$find = isset($_POST['regex'][$a]['find'])?$_POST['regex'][$a]['find']:'';
					$replace =  preg_replace_callback(
								 '`(?<!\\\\)\\\\([rnt])`'
								,function($matches)
									{
										switch($matches[1])
										{
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
									}
								,isset($_POST['regex'][$a]['replace'])?$_POST['regex'][$a]['replace']:''
							);
					$modifiers = isset($_POST['regex'][$a]['modifiers'])?$_POST['regex'][$a]['modifiers']:'';
					$makeTextarea = isset($_POST['regex'][$a]['makeTextarea'])?true:false;
					$tmp = new regex_check_child_model( $find , $replace , $modifiers , $makeTextarea );
					$this->regexes[] = $tmp;

					for( $b = 0 ; $b < count($output) ; $b +=1 )
					{
						$output[$b] = $tmp->process($output[$b]);
					}
				}
				if( $this->split_sample === true )
				{
						$output = implode( $imploder , $output );
				}
				else
				{
					$output = $output[0];
				}
				if( $tmp_output !== $output )
				{
					if( $this->ws_trim === true && $this->ws_action_after === true )
					{
						$output = trim($output);
					}
					$this->output = $output;
					$this->output_is_different = true;
				}
			}
			else
			{
				$this->regexes[] = new regex_check_child_model('','','',false);
			}
			if( isset($_POST['submit_replace']) )
			{
				$this->test_only = false;
			}
			$this->request_uri = $_SERVER['REQUEST_URI'];
		}
	}

	private function fix_white_spaces( $input )
	{
		return preg_replace_callback(
			 '`(?<!\\\\)\\\\([rnt])`'
			,function($matches)
				{
					switch($matches[1])
					{
						case 'n': return "\n";
							break;
						case 'r': return "\r";
							break;
						case 't': return "\t";
							break;
					}
				}
			,$input
		);
	}

	public function get_prop( $prop_name )
	{
		if( is_string($prop_name) && $prop_name != '' && property_exists($this,$prop_name) )
		{
			return $this->$prop_name;
		}
		else
		{
			// throw
		}
	}

	public function set_len_not_ok( $len = 'sample' )
	{
		if( $len == 'sample' )
		{
			$this->sample_len_ok = false;
		}
		if( $len == 'matched' )
		{
			$this->matched_len_ok = false;
		}
	}
}