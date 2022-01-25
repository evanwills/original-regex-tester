<?php
/**
 * This file set some shared variables and pulls in the debug()
 * function
 *
 * PHP Version 5.4, 7.x, 8.0
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */

// ==================================================================
// START: debug include

if (!function_exists('debug')) {
    if (isset($_SERVER['HTTP_HOST'])) {
        $path = $_SERVER['HTTP_HOST'];
        $pwd = dirname($_SERVER['SCRIPT_FILENAME']) . '/';
    } else {
        $path = $_SERVER['USER'];
        $pwd = $_SERVER['PWD'] . '/';
    };
    if (substr_compare($path, '192.168.', 0, 8) == 0) {
        $path = 'localhost';
    }
    switch ($path) {
    case '192.168.18.128':    // work laptop (debian)
    case 'antechinus':    // work laptop (debian)
    case 'localhost':    // home laptop
    case 'evan':        // home laptop
    case 'wombat':
        $root = '/var/www/';
        $inc = $root . 'includes/';
        $classes = $cls = $root . 'classes/';
        break; // home laptop

    case '192.168.18.129':    // work laptop (debian)
    case 'bilby':
    case 'pademelon':
    case 'shingleback':
        $root = '/var/www/html/';
        $inc = $root . 'includes/';
        $classes = $cls = $root . 'classes/';
        break; // home laptop

    case 'webapps.acu.edu.au':       // ACU
    case 'panvpuwebapps01.acu.edu.au': // ACU
    case 'test-webapps.acu.edu.au':       // ACU
    case 'panvtuwebapps01.acu.edu.au': // ACU
    case 'dev-webapps.acu.edu.au':       // ACU
    case 'panvduwebapps01.acu.edu.au': // ACU
    case 'evwills':
        if (isset($_SERVER['HOSTNAME'])
            && $_SERVER['HOSTNAME'] = 'panvtuwebapps01.acu.edu.au'
        ) {
            $root = '/home/evwills/';
            $inc = $root . 'includes/';
            $classes = $cls = $root . 'classes/';
            break; // ACU
        } else {
            $root = '/var/www/html/mini-apps/';
            $inc = $root . 'includes_ev/';
            $classes = $cls = $root . 'classes_ev/';
            break; // ACU
        }
    };

    set_include_path(
        get_include_path() . PATH_SEPARATOR .
        $inc . PATH_SEPARATOR .
        $cls . PATH_SEPARATOR .
        $pwd
    );

    if (file_exists($inc . 'debug.inc.php')) {
        if (!file_exists($pwd . 'debug.info')
            && is_writable($pwd)
            && file_exists($inc . 'template.debug.info')
        ) {
            copy($inc . 'template.debug.info', $pwd . 'debug.info');
        };
        include $inc . 'debug.inc.php';
    } else {
        /**
         * Dummy debug function
         *
         * @return void
         */
        function debug()
        {
        }
    }
}

// END: debug include
// ==================================================================

/**
 * Convert a string to camelCase
 *
 * @param string $input string to be converted
 *
 * @return string camelCase string
 */
function toCamel($input)
{
    return preg_replace_callback(
        '/[-_ ]([a-z])/i',
        function ($matches) {
            return strtoupper($matches[1]);
        },
        $input
    );
}

/**
 * Convert function escaped white space sequences to normal white
 * space characters
 *
 * @param string $input String to be converted
 *
 * @return string Converted string
 */
function escaped2ws($input)
{
    return preg_replace_callback(
        '`(?<!\\\\)\\\\([rnt])`',
        function ($matches) {
            switch ($matches[1]) {
            case 'n':
                return "\n";

            case 'r':
                return "\r";

            case 't':
                return "\t";
            }
        },
        $input
    );
}

/**
 * Convert function normal white space characters to escaped white
 * space sequences
 *
 * @param string $input String to be converted
 *
 * @return string Converted string
 */
function ws2escaped($input)
{
    return str_replace(
        array("\n", "\r", "\t"),
        array('\n', '\r', '\t'),
        $input
    );
}

// ==================================================================


$sample = '';
$checker = array();
$wsTrim = false;
$ws_action = 'after';
$pairs = '';
$output = '';
$results = '';
$extra_tabs = '';
