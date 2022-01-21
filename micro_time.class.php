<?php

abstract class MicroTime
{
    const REGEX = '/^0(\.[0-9]+) ([0-9]+)$/';
    protected function __construct() {}

    /**
     * @method mt_subtract() retuns the time difference between two
     * microtime results
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    abstract public function mt_subtract($start, $end);

    /**
     * Get the microtime handler that will work on this system
     *
     * @param void
     * 
     * @return MicroTime
     */
    public static function get_obj()
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

class MicroTimeBasic extends MicroTime
{
    /**
     * @method mt_subtract() retuns the time difference between two
     * microtime results
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    public function mt_subtract($start, $end)
    {
        $_end = preg_replace(self::REGEX, '\2\1', $end);
        $_start = preg_replace(self::REGEX, '\2\1', $start);

        return $_end - $_start;
    }
}

class MicroTimeBC extends MicroTime
{
   /**
     * @method mt_subtract() retuns the time difference between two
     * microtime results
     *
     * Because of PHPs rounding off of floats when you're finding the
     * difference between two microtime values it's necessary to fiddle
     * with the floating point precision
     *
     * @param string $start microtime() from just before regex was run
     * @param string $end microtime() from just after regex was run
     *
     * @return string difference between $start and $end
     */
    public function mt_subtract( $start , $end )
    {
        return bcsub(
             preg_replace(self::REGEX, '\2\1', $end)
            ,preg_replace(self::REGEX, '\2\1', $start)
            ,8
        );
    }
}

class MicroTimeGMP extends MicroTime
{
/**
 * @method mt_subtract() retuns the time difference between two
 * microtime results
 *
 * Because of PHPs rounding off of floats when you're finding the
 * difference between two microtime values it's necessary to fiddle
 * with the floating point precision
 *
 * @param string $start microtime() from just before regex was run
 * @param string $end microtime() from just after regex was run
 *
 * @return string difference between $start and $end
 */
    public function mt_subtract( $start , $end )
    {
        return preg_replace(
              '/(?<=\.[0-9]{8}).*$/'
             ,''
             ,gmp_strval(
                gmp_sub(
                     preg_replace( self::REGEX , '\2\1' , $end )
                    ,preg_replace( self::REGEX , '\2\1' , $start )
                )
             )
        );
                    
    }
}
