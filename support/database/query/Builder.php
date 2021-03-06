<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 22:24
 */

namespace support\database\query;

use support\Object;

/**
 * Class Builder
 * @package support\database\query
 */
class Builder extends Object
{

    /**
     * @var db
     */
    public $db;

    /**
     * @var string  占位符
     */
    private $BLANK = ' ';


    /**
     * @param Query $Query
     */
    public function build( Query $Query )
    {

        $sql[] = $this->buildSelect($Query->select);
        $sql[] = $this->buildTable($Query->table);
        $sql[] = $this->buildJoin($Query->join);
        $sql[] = $this->buildWhere($Query->where);
        $sql[] = $this->buildGroup($Query->group);
        $sql[] = $this->buildHaving($Query->having);
        $sql[] = $this->buildOrder($Query->order);
        $sql[] = $this->buildLimit($Query->limit);

        return join($this->BLANK,$sql);
    }

    /**
     * @param null $condition
     * @return string
     */
    public function buildSelect( $condition = null )
    {
        $select = [];
        if( ! empty( $condition ) && is_array($condition) ){
            foreach( $condition as $item ){
                $select[] = $this->prepareCondition($item);
            }
        }
        return 'SELECT ' . (  $select == [] ? '*' : join(',',$select) );
    }

    /**
     * @param null $condition
     * @param string $prefix
     * @return string
     */
    public function buildTable( $condition = null ,$prefix = 'FROM ')
    {

        if( ! empty( $condition ) && is_array( $condition ) ){
            $table = [];
            foreach( $condition as $item ){
                $table[] =$this->prepareCondition($item);
            }
            return  $prefix.join(',',$table);
        }else {
            return '';
        }
    }

    /**
     * @param null $condition
     * @return string
     */
    public function buildJoin( $condition =null )
    {
        if( ! empty( $condition ) && is_array( $condition ) ){
            $join = [];
            foreach( $condition as $item ){
                $join[] =$this->prepareCondition($item);
            }
            return  ' '.join(' ',$join);
        }else {
            return '';
        }
    }

    /**
     * @param null $condition
     * @return mixed
     */
    public function buildWhere( $condition = null ){
        $where = [];
        if( !empty($condition) && is_array( $condition ) ){
            foreach($condition as $item){
                $where[] = $this->prepareCondition($item);
            }
            return 'WHERE '.join(' AND ',$where);
        }else{
            return '';
        }
    }

    /**
     * @param null $condition
     * @return string
     */
    public function buildGroup( $condition = null )
    {
        if( !empty($condition) && is_array($condition) ){
            $group =[];
            foreach($condition as $item){
                $group[] = $this->prepareCondition($item);
            }
            return 'GROUP BY '.join(',',$group);
        }else{
            return '';
        }
    }

    /**
     * @param null $condition
     * @return string
     */
    public function buildHaving( $condition = null )
    {
        if( !empty( $condition ) && is_array($condition) ){
            $having = [];
            foreach( $condition as $item){
                $having[] = $this->prepareCondition($item);
            }
            return 'HAVING '.join(' AND ',$having);
        }else{
            return '';
        }
    }

    /**
     * @param null $condition
     * @return string
     */
    public function buildOrder( $condition = null )
    {

        if( !empty($condition) && is_array($condition) ){
            $order =[];
            foreach($condition as $item){
                $order[] = $this->prepareCondition($item);
            }
            return 'ORDER BY '.join(',',$order);
        }
        return '';
    }

    /**
     * @param null $condition
     * @return string
     */
    public function buildLimit( $condition = null )
    {
        if( ! empty($condition) && is_array($condition) ){

            return 'LIMIT '.join(',',$condition);
        }
        return '';
    }

    /**
     * @param null $condition
     * @return mixed|null|string
     */
    public function prepareCondition( $condition = null)
    {
        $result = '';
        if($condition === null){
            return null;
        }
        if( is_string($condition) ){
            return $condition;
        }
        if( is_int($condition)){
            return $condition;
        }
        if( $condition instanceof Expression ){
            return $condition->getValue();
        }
        if( $condition instanceof Query){
            return '( '. $condition  .' )';
        }
        if( isset($condition[0]) ){
            $condition[0] = $this->prepareCondition($condition[0]);
        }
        if( isset($condition[2]) ){
            $condition[2] = $this->prepareCondition($condition[2]);
        }
        if( isset($condition[1]) ){
            switch( $condition[1] ){
                //别名
                case Query::_AS :
                    $result = $condition[0].$this->BLANK.Query::_AS.$this->BLANK.$condition[2];
                    break;
                //连接
                case Query::JOIN  :
                case Query::LEFTJOIN :
                case Query::RIGHTJOIN :
                    $result = $condition[1].$this->BLANK.$condition[0].' ON '.$condition[2];
                    break;
                //函数
                case Query::F_COUNT :
                case Query::F_SUM :
                case Query::F_AVG :
                    $result =  $condition[1].'('.$condition[0].')' ;
                    break;
                //排序
                case Query::ASC :
                case Query::DESC :
                    $result = $condition[0] .$this->BLANK. $condition[1];
                    break;

                //条件判断 is null
                case Query::IS_NULL :
                case Query::IS_NOT_NULL :
                    $result = $condition[0] .$this->BLANK. $condition[1];
                    break;
                case Query::IN :
                case Query::NOT_IN :
                    $result = $condition[0] .$this->BLANK. $condition[1] .$this->BLANK. $condition[2];
                    break;
                //TODO::  LIKE  EXIEXT IN regexp
                case Query::BETWEEN:
                case Query::NOT_BETWEEN :
                    $result = $condition[0] .$this->BLANK. $condition[1] ." '". $condition[2] ."' AND '".$condition[3]."'";
                    break;
                default :
                    if( isset($condition[2]) ){
                        $result =  $condition[0].$this->BLANK.$condition[1].$this->BLANK.$condition[2];
                    }
                    break;
            }
        }else {
            return $condition[0];
        }
        return $result;
    }

    /**
     * @param InsertQuery $Query
     * @return array
     * INSERT INTO TABLE ABC ()VALUES();
     */
    public function buildInsert(InsertQuery $Query )
    {
        $sql[] = $this->buildTable($Query->table,'INSERT INTO ');
        $sql[] = $this->buildInsertParams($Query->getOneTable() ,$Query->params);

        return join($this->BLANK,$sql);
    }

    /**
     * @param $table
     * @param $params
     * @return string
     */
    public function buildInsertParams($table ,$params )
    {
        $tableSchema = $this->db->getTableSchema($table);

        $data = [];
        foreach( $tableSchema->columns as $colKey =>$colObj )
        {
            $value = isset($params[$colKey])
                ?  $params[$colKey]
                :  $colObj->defaultValue ;
            // 处理下
            if(is_string($value)){
                $value = $this->db->quoteValue($value);
            }else if( $value === false ){
                $value = 0;
            }else if($value === null){
                $value = 'NULL';
            }

            $colKey = $this->db->quoteColumn($colKey);

            $data[$colKey] =$value;
        }
        return  $data ? "(".join(',',array_keys($data)).") VALUES (".join(',',array_values($data)).")" : '';
    }


    /**
     *
     * @param UpdateQuery
     * @return mixed
     */
    public function buildUpdate(UpdateQuery $Query)
    {

        $sql[] = $this->buildTable($Query->table,'UPDATE ');
        $sql[] = $this->buildUpdateSet($Query->getOneTable(),$Query->params);
        $sql[] = $this->buildWhere($Query->where);

        return join($this->BLANK,$sql);
    }

    /**
     * @param $table
     * @param $params
     * @return string
     */
    public function buildUpdateSet($table,$params)
    {
        $tableSchema = $this->db->getTableSchema($table);
        $set =[];
        $columns = $tableSchema->columns();

        foreach($params as $key=>$value)
        {
            if( ! in_array($key,$columns) || in_array($key,$tableSchema->primary()) ){
                continue;
            }
            //消灭主键
            if( is_string($value) ){
                $value = $this->db->quoteValue($value);
            }else if( $value === false ){
                $value = 0;
            }else if($value === null){
                $value = 'NULL';
            }
            $key = $this->db->quoteColumn($key);
            $set[] = $key .'='. $value;
        }

        return 'SET '.join(',',$set);
    }


    /**
     * @param DeleteQuery $Query
     * @return mixed
     */
    public function buildDelete(DeleteQuery $Query)
    {
        $sql[] = $this->buildTable($Query->table,'DELETE FROM ');
        $sql[] = $this->buildWhere($Query->where);
        return join($this->BLANK,$sql);
    }
}