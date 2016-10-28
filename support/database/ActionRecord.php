<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 00:30
 */

namespace support\database;

use support\database\query\DeleteQuery;
use support\database\query\InsertQuery;
use support\database\query\UpdateQuery;
use support\helper\Arr;
use support\model\Model;

/**
 * Class ActionRecord
 * @package support\database
 */
class ActionRecord extends Model
{
    use ActionRecordTrait;

    protected $db;

    protected $tableSchema;

    protected $primary;

    protected $denyUpload = [];

    private $loadFromDb = false;

    public function init()
    {
        parent::init();

        $this->db = static::getDb();
        $this->tableSchema = $this->db->getTableSchema( static::tableName() );
        $this->attFilter = $this->tableSchema->columns();
    }

    public function setPrimary( $primary )
    {
        if(is_array($primary))
        {
            $this->setAttributes($primary);
        }else{

            $keys = $this->tableSchema->primary();
            if(isset($keys[0])){
                $this->setAttribute( $keys[0] ,$primary );
            }
            //$this->setAttribute( $keys[0] ,$primary );
        }
    }

    public function setAttribute($attribute, $value)
    {
        if( $this->filterAttribute($attribute) == false ){
            return false;
        }

        if( ! isset($this->attributes[$attribute]) )
        {
            $this->attributes[$attribute] = $value;
            if( in_array($attribute ,$this->tableSchema->primary()) ){
                $this->primary[$attribute] = $value;
            }
            return true;
        }
        if($this->loadFromDb == true){
            if( in_array($attribute ,$this->denyUpload) ){
                return false;
            }
            if( in_array($attribute ,$this->tableSchema->primary()) ){
                return false;
            }
            if( ! isset($this->oldAttributes[$attribute]) ) {
                $this->oldAttributes[$attribute] = $this->attributes[$attribute];
            }
        }
        $this->attributes[$attribute] = $value;
        return true;
    }

    public function hasDirtyAttribute()
    {
        return count($this->oldAttributes) > 0;
    }

    public function getDirtyAttributes()
    {
        $keys = array_keys($this->oldAttributes);
        return Arr::filter($this->attributes ,$keys);
    }

    /**
     * @param null $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function save(  $data = null  )
    {
        if( $data ){
            $this->setAttributes($data);
        }
        if( $this->loadFromDb )
        {
            if( $this->hasDirtyAttribute() ){
                return $this->update();
            }
            return true;
        }

        if( ! empty($this->primary) ) {
            return $this->update();
        }else {
            return $this->insert();
        }
    }

    /**
     * @param null $data
     * @return mixed
     * @throws \Exception
     */
    public function insert( $data = null )
    {
        if( $data ){
            $this->setAttributes($data);
        }
        if( empty( $this->attributes) ){
            //错误信息
            return false;
        }

        $transaction = $this->db->getTransaction();
        $transaction->begin();
        $level = $transaction->getLevel();
        try{

            $result = (new InsertQuery( static::tableName() , null ,['db'=>$this->db] ) )->insert( $this->attributes );

            if( $transaction->getLevel() == $level){
                $transaction->commit();
            }
            
            //TODO 自增ID
            if( $result > 0 ){
                //print_r($result);
                //$this->setPrimary($result);
            }
        }catch(\Exception $e) {
            if( $transaction->getLevel() == $level){
                $transaction->rollback();
            }
            throw $e;
        }

        return $result;
    }

    /**
     * @param null $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function update( $data = null )
    {
        if( $data ){
            $this->setAttributes($data);
        }
        if( $this->primary )
        {
            $data = $this->hasDirtyAttribute()
                ? $this->getDirtyAttributes()
                : $this->attributes;

            $transaction = $this->db->getTransaction();
            $transaction->begin();
            $level = $transaction->getLevel();
            try{

                $result = (new UpdateQuery(static::tableName(),null,['db'=>$this->db]))
                    ->where($this->primary)
                    ->update($data);

                if( $transaction->getLevel() == $level){
                    $transaction->commit();
                }
                return $result;
            }catch(\Exception $e){

                if( $transaction->getLevel() == $level){
                    $transaction->rollback();
                }
                throw $e;
            }
        }
        return false;
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function remove()
    {
        if( $this->primary ){

            $transaction = $this->db->getTransaction();
            $transaction->begin();
            $level = $transaction->getLevel();
            try{
                $result = (new DeleteQuery(static::tableName(),['db'=>$this->db]))
                    ->where($this->primary)
                    ->delete();

                if( $transaction->getLevel() == $level){
                    $transaction->commit();
                }
                return $result;
            }catch(\Exception $e){
                if( $transaction->getLevel() == $level){
                    $transaction->rollback();
                }
                throw $e;
            }
        }
        return false;
    }

    /**
     * @param $primary
     * @return bool
     */
    public function load( $primary )
    {
        $this->setPrimary( $primary );

        $attributes = static::find()->where($this->primary)->one();

        if( $attributes ){
            $this->setAttributes($attributes);
            $this->loadFromDb = true;
        }
        return false;
    }
}