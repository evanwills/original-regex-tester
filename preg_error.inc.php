<?php
/**
 * @function preg_is_valid() tests a regular expression to see if it
 * is valid
 * 
 * @param string $regex Regular expression to be tested
 * @param boolean $show_error If true, the error message is returned,
 *	  otherwise boolean is returned
 *
 * @return boolean true if the regex was valid, false otherwise
 * @return string if regex was invalid and show_error was true the
 *	  error message generated by the regex is returned
 */
/*
if( function_exists('preg_last_error') )
{
	function preg_is_valid( $regex , $show_error = false )
	{
		@preg_match($regex,'');
		$error = preg_last_error();
		if( $error == PREG_NO_ERROR )
		{
			return true;
		}
		else
		{
			if( $show_error !== true )
			{
				return false;
			}
			else
			{
				return $error;
			}
		}
	}
}
else
{
*/	function preg_is_valid( $regex , $show_error = false )
	{
		if($old_track_errors = ini_get('track_errors'))
		{
			$old_php_errormsg = isset($php_errormsg)?$php_errormsg:false;
		}
		else
		{
			ini_set('track_errors' , 1);
		}
		unset($php_errormsg);

		@preg_match($regex,'');

		$output = isset($php_errormsg)?$php_errormsg:true;
		if( $show_error !== true && $output !== true )
		{
			$output = false;
		}
	
		if($old_track_errors)
		{
			$php_errormsg = isset($old_php_errormsg)?$old_php_errormsg:false;
		}
		else
		{
			ini_set('track_errors' , 0);
		}

		return $output;
	}
/*
}
*/