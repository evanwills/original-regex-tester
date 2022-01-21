<?php

class regex_check_child_model
{
	private $regex;
	private $find = '';
	private $replace = '';
	private $modifiers = '';
	private $modifiers_original = '';
	private $multiline = false;
	private $multiline_cb = '';
	private $errors = array('modifiers' => '');
	private $report = array();

	private static $delim_open = '`';
	private static $delim_close = '`';


	public function __construct( $find , $replace , $modifiers , $multiline )
	{
		if( !is_string($find) )
		{
			$this->errors[] = '$find is not a string';
		}
		if( !is_string($replace) )
		{
			$this->errors[] = '$replace is not a string';
		}
		$this->find = $find;
		$this->replace = $replace;

		if( is_string($modifiers) )
		{
			$this->modifiers_original = $modifiers;
			$modifiers = str_split($modifiers);
			for( $a = 0 ; $a < count($modifiers) ; $a += 1 )
			{
				switch( $modifiers[$a] )
				{
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
						$this->modifiers .= $modifiers[$a];
						break;
					default:
						$this->errors['modifiers'] .= "\"{$modifiers[$a]}\" is not a valid modifier. ";
				}
			}
		}
		if( is_bool($multiline) )
		{
			$this->multiline = $multiline;
		}

		$this->regex = regex_replace::process( $this->get_regex() , $replace );
	}



	public function get_multiline()
	{
		return $this->multiline;
	}

	public function is_regex_valid()
	{
		return $this->regex->is_valid();
	}
	public function get_regex()
	{
		return self::$delim_open . $this->find . self::$delim_close . $this->modifiers;
	}

	public function get_find()
	{
		return $this->find;
	}

	public function get_replace()
	{
		return $this->replace;
	}

	public function get_modifiers( $original = true )
	{
		if( $original !== false )
		{
			return $this->modifiers_original;
		}
		else
		{
			return $this->modifiers;
		}
	}

	public function process( $sample )
	{
		if( is_array($sample) )
		{
			for( $a = 0 ; $a < count($sample) ; $a += 1 )
			{
				$this->report[] = $this->regex->report($sample[$a]);
				$sample[$a] = $this->regex->get_output($sample[$a]);
			}
			return $sample;
		}
		else
		{
			$this->report[] = $this->regex->report($sample);
			return $this->regex->get_output($sample);
		}
	}

	public function get_errors()
	{
		return array_merge($this->errors,$this->regex->get_errors());

	}

	public function get_report()
	{
		return $this->report;
	}

	public static function set_regex_delim( $delim )
	{
		if( is_string($delim) && strlen($delim) == 1 && preg_match('`^[^a-z0-9\s\\\\]$`',$delim) )
		{
			switch( $delim )
			{
				case '{':
				case '}':
					self::$delim_open = '{';
					self::$delim_close = '}';
					break;
				case '[':
				case ']':
					self::$delim_open = '[';
					self::$delim_close = ']';
					break;
				case '<':
				case '>':
					self::$delim_open = '<';
					self::$delim_close = '>';
					break;
				case '(':
				case ')':
					self::$delim_open = '(';
					self::$delim_close = ')';
					break;
				default:
					self::$delim_open = self::$delim_close = $delim;
			}
			return true;
		}
		return false;
	}

	public static function get_regex_delim()
	{
		return self::$delim_open;
	}

	public function set_delim( $delim )
	{
		return self::set_regex_delim($delim);
	}

	public function get_delim()
	{
		return self::$delim_open;
	}
}
