<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/24
 * Time: 10:36
 */

namespace support\router;


/**
 * 简单处理
 * 
 * Class AccessAuth
 * @package support\auth
 */
class AccessTable
{

    protected static $table = [];

    /**
     * @param $access
     */
    public function loadAccess( $access )
    {
        if($access){
            static::$table = $access;
        }
    }

    /**
     *
     * 允许 * 匹配
     * @param $route
     * @return null
     */
    public function getAccess( $route )
    {
        $matchReg = strtolower(  $route->control.'@*' );
        if( isset( static::$table[$matchReg] ) )
        {
            return static::$table[$matchReg] ;
        }

        $route =  strtolower(  $route->getRoute() );

        return isset( static::$table[$route] )
            ? static::$table[$route]
            : null;
    }
}