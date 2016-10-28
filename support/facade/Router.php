<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 19:27
 */

class Router extends \support\Facade
{
    protected static $router;

    protected static function instance()
    {
        if( !static::$router ){
            static::$router =\support\Application::getServer('router');
        }
        return static::$router;
    }
}