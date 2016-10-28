<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 00:31
 */

namespace support\database;

use support\database\query\Builder;
use support\database\query\Expression;

use Cache;
use support\database\schema\TableSchema;
use support\Object;

class Schema extends Object
{

    public $db;

    public $builder;


    public function getBuilder()
    {
        if( $this->builder == null ){
            $this->builder = new Builder(['db'=>$this->db]);
        }
        return $this->builder;
    }

    /**
     * @param $value
     * @return string
     * @see yii2
     */
    public function quoteValue( $value )
    {
        if( !is_string($value) ){
            return $value;
        }

        return "'" . addcslashes(str_replace("'", "''", $value), "\000\n\r\\\032") . "'";
    }

    /**
     * @param $table
     * @return string
     */
    public function quoteTableName( $table )
    {
        if(is_string($table) && strpos($table,'.') ){
            $tables =[];
            foreach(explode('.',$table)  as $t){
                $tables[] = $this->quoteSimpleTable($t);
            }
            return join('.',$tables);
        }

        return $this->quoteSimpleTable( $table );
    }

    /**
     * @param $table
     * @return string
     */
    public function quoteSimpleTable($table)
    {
        return strpos($table,'`') !== false
            ? $table
            : "`$table`";
    }

    /**
     * @param $column
     * @return mixed|string
     */
    public function quoteColumn( $column )
    {
        if($column == '*') {
            return $column;
        }
        if( $column instanceof Expression ){
            return $column->getValue();
        }

        if( is_string($column) && strpos($column,'.') ){
            $cols = [];
            foreach( explode('.',$column) as $col ){
                $cols[] = $this->quoteSimpleColumn( $col );
            }
            return join('.',$cols);
        }

        return $this->quoteSimpleColumn( $column );
    }

    /**
     * @param $column
     * @return string
     */
    public function quoteSimpleColumn( $column )
    {
        return strpos($column ,'`') !== false
            ? $column
            : "`$column`";
    }

    /**
     * @param $pointName
     * @return mixed
     */
    public function createSavePoint( $pointName )
    {
        return $this->db->createCommand("SAVEPOINT $pointName;")
            ->execute();
    }

    /**
     * @param $pointName
     * @return mixed
     */
    public function releaseSavePoint( $pointName )
    {
        return $this->db->createCommand("RELEASE SAVEPOINT $pointName;")
            ->execute();
    }

    /**
     * @param $pointName
     * @return mixed
     */
    public function rollBackSavePoint( $pointName )
    {
        return $this->db->createCommand("ROLLBACK TO SAVEPOINT $pointName;")
            ->execute();
    }

    /**
     * @param $level
     * @return mixed
     */
    public function setTransactionLevel( $level )
    {
        return $this->db->createCommand("SET TRANSACTION ISOLATION LEVEL $level;")
            ->execute();
    }

    /**
     * @param $table
     * @return mixed
     */
    protected function tableSchemaCacheKey( $table )
    {
        return md5(serialize(
            [
                'table' => $table
            ]
        ));
    }

    /**
     * 简单些
     * @param $table
     * @return TableSchema
     * @throws \Exception
     */
    public function getTableSchema( $table )
    {
        $cacheKey = $this->tableSchemaCacheKey( $table );
        if( ( $schema = Cache::get( $cacheKey ) ) == null ){
            $result = $this->db->createCommand('SHOW CREATE TABLE '.$table)->queryOne();
            if( ! isset($result['Create Table']) ){
                throw new \Exception('Not Found Table '.$table);
            }
            $tableSchema = new TableSchema();
            $tableSchema->tableName = $table;
            $columnResult = $this->db->createCommand('SHOW FULL COLUMNS FROM '.$table)->queryAll();
            foreach($columnResult as $col)
            {
                $column = new $tableSchema->ColumnClass();
                $column->name = $col['Field'];
                $column->allowNull = ($col['Null'] == 'YES') ;
                $column->isPrimary =($col['Key'] == 'PRI' ) ;
                $column->defaultValue = $col['Default'] ? $col['Default'] : NULL ;
                $column->autoIncrement = $col['Extra'] == 'auto_increment' ;
                if(preg_match('#^(\w+)(?:\(([^\)]+)\))?#',$col['Type'],$match)){
                    if(isset($match[1])){
                        $column->type = $match[1];
                    }
                    if(isset($match[2])){
                        $column->size = $match[2];
                    }
                }

                if( $column->type === 'timestamp' && $col['Default'] == 'CURRENT_TIMESTAMP' )
                {
                    $column->defaultValue = new Expression('CURRENT_TIMESTAMP');
                }

                $tableSchema->columns[$column->name] = $column;
                if( $column->isPrimary ){
                    array_push($tableSchema->primaryKey,$column->name);
                }
            }
            //
            Cache::set( $cacheKey ,serialize( $tableSchema ) );
            return $tableSchema;
        }
        return  unserialize( $schema );
    }


    public function getLastInsertId()
    {
        return $this->db->pdo->lastInsertId();
    }
}