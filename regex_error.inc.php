<?php

/**
 * Test whether a supplied regex generates any error messages
 *
 * Takes a supplied regular expression and runs it through
 * the appropriate PHP core function trapping any errror
 * message generated and returns it.
 *
 * @param string $regex Regular expression to be tested
 *
 * @return string|false If the supplied regular expression generated
 *                      an error the error string is returned
 *                      otherwise FALSE is returned
 */
function regex_debug_php($regex)
{
    if ($old_track_errors = ini_get('track_errors')) {
        $old_php_errormsg = isset($php_errormsg)
            ? $php_errormsg
            : false;
    } else {
        ini_set('track_errors', 1);
    }

    unset($php_errormsg);

    @preg_match($regex, '');
    $flav = 'PCRE';

    $output = isset($php_errormsg)
        ? $php_errormsg
        : 'Supplied '.$flav.' regular expression is valid';

    if ($old_track_errors) {
        $php_errormsg = isset($old_php_errormsg)
            ? $old_php_errormsg
            : false;
    } else {
        ini_set('track_errors', 0);
    }

    return $output;
}


