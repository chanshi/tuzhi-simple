<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 22:41
 */


class Config extends \support\Facade
{
    static $config;

    protected static function instance()
    {
        if(static::$config == null ){
            static::$config = App::getServer('config');;
        }
        return static::$config;
    }
}