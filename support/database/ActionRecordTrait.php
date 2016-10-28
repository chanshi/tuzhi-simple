<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/15
 * Time: 00:24
 */

namespace support\database;

use support\database\query\DeleteQuery;
use support\database\query\Query;
use DB;
use support\database\query\UpdateQuery;

/**
 * Class ActionRecordTrait
 * @package support\database
 */
trait ActionRecordTrait
{

    public static function getDb()
    {
        return DB::getDb();
    }

    /**
     * @return mixed
     */
    public static function tableName()
    {
        return strtolower( basename( str_replace('\\','//',get_called_class())) );
    }

    /**
     * @param null $Columns
     * @return $this
     */
    public static function find( $Columns = null )
    {
        $Query = (new Query( ['db'=> static::getDb() ] ))->table( static::tableName() );
        $Columns
            ? $Query->select($Columns) :
            null;
        return $Query;
    }

    /**
     * @return static
     */
    public static function getNewRecord()
    {
        return new static();
    }

    public static function addRecord(){}

    /**
     * 修改
     */
    public static function modify()
    {
        return (new UpdateQuery(static::tableName(),['db'=> static::getDb()]));
    }

    /**
     * @return DeleteQuery
     */
    public static function delete()
    {
        return (new DeleteQuery(static::tableName(),['db'=> static::getDb()]));
    }

    /**
     * @param $condition
     * @return bool
     */
    public static function exists( $condition )
    {
        $result = static::find(DB::Expression('COUNT(*) as has'))->where($condition)->one();
        return isset($result['has']) && $result['has'] >  0 ;
    }

}