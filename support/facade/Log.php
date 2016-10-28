<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/6/15
 * Time: 13:59
 */


/**
 *
 * Log::error($message);
 * Class Log
 */
class Log extends \support\Facade
{
    static $log;

    protected static function instance()
    {
        if(static::$log == null ){
            static::$log = App::getServer('log');
        }
        return static::$log;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = static::instance();
        $method = strtolower($name);
        $type = array_flip( \support\Log::$Type );
        if( isset($type[$method]) ){
            $arguments[1] = $type[$method];
            return call_user_func_array([ $instance ,'record'] ,$arguments);
        }
        throw new \Exception('Not Found Method '.$method.' In Cache ');
    }
}