<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 19:26
 */

use support\Application;

class App extends \support\Facade
{

    public static $app;

    protected static function instance()
    {
        if( ! static::$app ){
            static::$app = Application::$app;
        }
        return static::$app;
    }
    
}