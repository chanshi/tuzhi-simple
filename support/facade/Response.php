<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 19:28
 */

class Response extends \support\Facade
{
    protected static $response;

    protected static function instance()
    {
        if( !static::$response ){
            static::$response =\support\Application::getServer('response');
        }
        return static::$response;
    }
}