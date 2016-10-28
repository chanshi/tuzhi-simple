<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 19:55
 */

class DB extends \support\Facade
{
    /**
     * @var
     */
    protected  static $db;

    /**
     * @return mixed
     * @throws Exception
     */
    protected static function instance()
    {
        if( static::$db == null ){
            $config = Config::get('db');
            if( empty($config) ){
                throw new Exception('Not Found Database Config');
            }
            static::$db = new \support\database\Connection( $config );
        }
        return static::$db;
    }

    /**
     * @param $sql
     * @return mixed
     */
    public static function sql( $sql )
    {
        static::instance();
        return static::$db->createCommand($sql);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getDb()
    {
        return static::instance();
    }

    /**
     * @return \support\database\query\Query
     */
    public static function Query()
    {
        return new \support\database\query\Query();
    }

    /**
     * @param $value
     * @return \support\database\query\Expression
     */
    public static function Expression( $value )
    {
        return new \support\database\query\Expression($value);
    }
}