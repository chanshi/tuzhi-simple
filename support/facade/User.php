<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/25
 * Time: 17:46
 */

class User extends \support\Facade
{
    protected static $user;

    protected static function instance()
    {
        if( !static::$user ){
            static::$user =\support\Application::getServer('user');
        }
        return static::$user;
    }


    public static function get( $attribute )
    {
        return static::instance()->getAttribute($attribute);
    }
}