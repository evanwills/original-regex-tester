<?php

/**
 * Use PHP's inbuilt preg_quote() function to do the same thing
 * faster
 */

/**
 * Prepair a string to be used within a regular expression.
 *
 * Use PHP's inbuilt preg_quote() function to do the same thing
 * faster
 *
 * It takes a string and escapes all PHP PCRE metacharacters. It
 * assumes that the string is NOT already a regex but is to be used
 * within one.
 *
 * NOTE: In benchmarking tests I've done, the preg_replace() method
 *       generally performed about a third faster than the
 *       str_replace() version.
 *
 * @param string $input       Input to be sanitised
 * @param mixed  $safe_method NULL = let the system decide,
 *                            'regex' = force preg_replace() method,
 *                            'str' = force str_replce() method
 *
 * @return string regex safe (all PCRE metacharacters escaped)
 */
function regex_safe($input, $safe_method = null)
{
    // if (!defined('REGEX_SAFE__USE_REGEX') && $safe_method == null) {
    //     $sample = ' [ ] { { / ^ $ & ? * + ';
    //     $test_sample = ' \[ \] \{ \{ \\/ \^ \$ & \? \* \+ ';
    //     $test_output = preg_replace(
    //         '/([\/\\\[\]^$.()?*{}+])/',
    //         '\\\\\1',
    //         $sample
    //     );
    //     define(
    //         'REGEX_SAFE__USE_REGEX',
    //         ($test_sample == $test_output)
    //     );
    // }

    // if (!is_string($input)) {
    //     die('regex_safe() parameter must be a string');
    // }

    // switch ($input) {
    // case "\n":
    //     $output = '\\n';
    //     break;
    // case "\r":
    //     $output = '\\r';
    //     break;
    // case "\t":
    //     $output = '\\t';
    //     break;
    // case "\r\n":
    //     $output = '\\r\\n';
    //     break;
    // case "\n\r":
    //     $output = '\\n\\r';
    //     break;
    // default:
    //     if ($safe_method == 'regex'
    //         || ($safe_method == NULL && REGEX_SAFE__USE_REGEX == TRUE)
    //     ) {
    //         $output = preg_replace(
    //             '/([\/\\\[\]^$.()?*{}+])/',
    //             '\\\\\1', // Yes the five backslashes are required!!!
    //             $input
    //         );
    //     } else {
    //         $output = str_replace(
    //             array(
    //                 '\\', '/', '^', '$', '.', '[', ']',
    //                 '|', '(', ')', '?', '*', '+', '{', '}'
    //             ),
    //             array(
    //                 '\\\\', '\/', '\^', '\$', '\.',
    //                 '\[', '\]', '\|', '\(', '\)', '\?', '\*',
    //                 '\+' ,'\{' ,'\}'),
    //             $input
    //         );
    //     }
    // }

    // return $output;

    return preg_quote($input);
}
