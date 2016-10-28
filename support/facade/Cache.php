<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 22:13
 */

use support\cache\Cache as cacheClass;
use support\cache\ICache;


class Cache extends \support\Facade
{

    /**
     * @var
     */
    protected static $cache;

    /**
     * @var array 支持的方法
     */
    protected static $InterfaceMethod =
        [
            'set',
            'get',
            'delete',
            'flush',
            'decrement',
            'increment'
        ];

    /**
     * @return mixed
     * @throws Exception
     */
    protected static function instance()
    {
        if( static::$cache == null ){
            $config = Config::get('cache');
            if( empty($config) ){
                throw new Exception('Not Found Cache Configure');
            }
            static::$cache = new cacheClass( $config );
        }
        return static::$cache;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($method, $arguments)
    {
        $Cache = static::instance();

        $method = strtolower($method);
        if( isset($Cache->support[$method]) ){
            return $Cache->getInstance($method);
        }
        
        $defaultCache = $Cache->getInstance();

        if( $defaultCache instanceof ICache && in_array($method ,Cache::$InterfaceMethod)){
            return call_user_func_array( [ $defaultCache , $method ] ,$arguments );
        }

        throw new \Exception('Not Found Method '.$method.' In Cache ');
    }
}