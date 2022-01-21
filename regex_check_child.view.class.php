<?php

abstract class regex_check_child_view
{

	static protected $sample_len = 300;
	static protected $matched_len = 300;


	abstract public function get_regex_fieldset_item( $index , regex_check_child_model $model  );

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
	abstract public function format_report( regex_check_child_model $model );

	public static function set_len( $input , $type = false )
	{
		if( is_int($input) && $input > 6 )
		{
			if( $type !== 'matched' )
			{
				self::$sample_len = $input;
			}
			if( $type !== 'sample' )
			{
				self::$matched_len = $input;
			}
			return true;
		}
		return false;
	}

	protected function trim_string( $input , $type = 'sample' )
	{
		if( $type === 'matched' )
		{
			$len = self::$matched_len;
		}
		else
		{
			$len = self::$sample_len;
		}
		if( strlen($input) > $len )
		{
			$len -= 3;
			return substr( $input , 0 , $len ).'...';
		}
		return $input;
	}
}