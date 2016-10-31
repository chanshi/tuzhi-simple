<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/15
 * Time: 00:24
 */

namespace support\database;

use support\database\query\DeleteQuery;
use support\database\query\InsertQuery;
use support\database\query\Query;
use DB;
use support\database\query\UpdateQuery;

/**
 * Class ActionRecordTrait
 * @package support\database
 */
trait ActiveRecordTrait
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
     * @return mixed
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
     * 简单粗暴
     * @param $data
     * @return mixed
     */
    public static function insert( $data = null)
    {
        return (new InsertQuery(static::tableName(), $data, ['db'=>static::getDb()] ))
            ->insert();
    }

    /**
     * @param null $data
     * @return UpdateQuery
     */
    public static function update( $data =null)
    {
        return (new UpdateQuery(static::tableName(),$data,['db'=> static::getDb()]));
    }

    /**
     * @return DeleteQuery
     */
    public static function delete()
    {
        return (new DeleteQuery(static::tableName(),['db'=> static::getDb()]));
    }

    /**
     * @param null $primary
     * @return static
     */
    public static function getNewRecord($primary = null )
    {
        $Object = new static();
        if( $primary ){
            $Object->load($primary);
        }
        return $Object;
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