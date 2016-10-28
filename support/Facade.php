<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 19:28
 */

namespace support;

/**
 * Class Facade
 * @package support
 */
class Facade
{
    /**
     *
     */
    protected static function instance(){}

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = static::instance();

        if( method_exists( $instance ,$name ) ){
            return call_user_func_array([$instance,$name],$arguments);
        }

        throw new \Exception('Not Found Method In '.get_class($instance).' Name');
    }
}