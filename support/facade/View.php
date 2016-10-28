<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 19:27
 */

class View extends \support\Facade
{
    protected static $view;

    protected static function instance()
    {
        if(static::$view == null)
        {
            $config = Config::get('view');
            if(empty($config)){
                throw new Exception('Not Found View Configure');
            }
            static::$view = new \support\View( $config );
        }
        return static::$view;
    }
}