<?php
/**
 * This file contains three classes for handling microtime maths
 * based on what the environment is capable of
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
 * MicroTime sets up all the methods for child microtime classes
 *
 * These classes have only one method: mtSub (Microtime subtract).
 * Because of the precision required to do timing calculations, we
 * want the best possible functionality to do our calculations
 *
 * This class has a single factory method which returns the best
 * class for the system the application is running on
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
abstract class MicroTime
{
    const REGEX = '/^0(\.[0-9]+) ([0-9]+)$/';

    /**
     * Constructor
     */
    protected function __construct()
    {
    }

    /**
     * Get the time difference between two microtime results
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end   microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    abstract public function mtSub($start, $end);

    /**
     * Get the microtime handler that will work on this system
     *
     * @return MicroTime
     */
    public static function getObj()
    {
        if (function_exists('bcsub')) {
            return new MicroTimeBC();
        } elseif (function_exists('gmp_strval')) {
            return new MicroTimeGMP();
        } else {
            return new MicroTimeBasic();
        }
    }
}

/**
 * MicroTime sets up all the methods for child microtime classes
 * using basic PHP subtract functionality
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class MicroTimeBasic extends MicroTime
{
    /**
     * Get the time difference between two microtime results using
     * default PHP subtract functionality
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end   microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    public function mtSub($start, $end)
    {
        $_end = preg_replace(self::REGEX, '\2\1', $end);
        $_start = preg_replace(self::REGEX, '\2\1', $start);

        return $_end - $_start;
    }
}

/**
 * MicroTime sets up all the methods for child microtime classes
 * using PHP's BCMath Arbitrary Precision Mathematics
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class MicroTimeBC extends MicroTime
{
    /**
     * Get the time difference between two microtime results using bcsub()
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end   microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    public function mtSub($start, $end)
    {
        return bcsub(
            preg_replace(self::REGEX, '\2\1', $end),
            preg_replace(self::REGEX, '\2\1', $start),
            8
        );
    }
}

/**
 * MicroTime sets up all the methods for child microtime classes
 * using PHP's GNU Multiple Precision
 *
 * @category RegexTest
 * @package  RegexTest
 * @author   Evan Wills <evan.wills@gmail.com>
 * @license  GPL2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     https://github.com/evanwills/original-regex-tester
 */
class MicroTimeGMP extends MicroTime
{
    /**
     * Get the time difference between two microtime results using
     * GMP functions
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end   microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    public function mtSub($start, $end)
    {
        return preg_replace(
            '/(?<=\.[0-9]{8}).*$/',
            '',
            gmp_strval(
                gmp_sub(
                    preg_replace(self::REGEX, '\2\1', $end),
                    preg_replace(self::REGEX, '\2\1', $start)
                )
            )
        );
    }
}
