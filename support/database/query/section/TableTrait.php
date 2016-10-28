<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/4/29
 * Time: 15:33
 */

namespace support\database\query\section;
use support\database\query\Query;


/**
 * Class TableTrait
 * @package tuzhi\db\query\section
 */
trait TableTrait
{

    /**
     * @var
     */
    public $table;

    public $join;


    private function quoteTable( $table )
    {
        if(is_string($table)){
            $table = $this->db->quoteTable($table);
        }
        return $table;
    }

    /**
     * @param $table or  Query
     * @param null $alias
     * @return $this
     */
    public function table( $table ,$alias = null )
    {
        $this->quoteTable($table);
        $this->table[] = $alias
            ? [$table, Query::_AS ,$alias]
            : [$table];

        return $this;
    }

    /**
     * 这个 
     * @return null
     */
    public function getOneTable()
    {
        if( count( $this->table ) == 1 && isset($this->table[0][0]) )
        {
            return $this->table[0][0];
        }
        return null;
    }

    /**
     * @param $table
     * @param $alias
     * @param null $condition
     * @return $this
     */
    public function join( $table , $alias , $condition = null)
    {
        $this->quoteTable($table);
        $this->join[] =[[$table,Query::_AS,$alias] ,Query::JOIN, $condition ];

        return $this;
    }

    /**
     * @param $table
     * @param $alias
     * @param null $condition
     * @return $this
     */
    public function leftJoin($table , $alias, $condition = null)
    {
        //TODO:: BUG 稍后修复
        $this->quoteTable($table);

        $this->join[] =[[$table,Query::_AS,$alias] ,Query::LEFTJOIN , $condition ];

        return $this;
    }

    /**
     * @param $table
     * @param $alias
     * @param null $condition
     * @return $this
     */
    public function rightJoin($table , $alias , $condition = null)
    {
        $this->quoteTable($table);

        $this->join[] =[[$table,Query::_AS,$alias] ,Query::RIGHTJOIN,$condition ];

        return $this;
    }

}