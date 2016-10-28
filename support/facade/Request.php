<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 19:27
 */

class Request extends \support\Facade
{
    protected static $request;

    protected static function instance()
    {
        if( ! static::$request ){
            static::$request = \support\Application::getServer('request');
        }
        return static::$request;
    }
}