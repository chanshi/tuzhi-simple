<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 22:38
 */

namespace support\database\schema;

class TableSchema
{

    public $ColumnClass = 'support\database\schema\ColumnSchema';

    public $tableName;

    public $columns = [];

    public $primaryKey = [];

    public function primary()
    {
        return $this->primaryKey;
    }

    public function columns()
    {
        return array_keys( $this->columns );
    }

    public function getColumn($column)
    {
        return isset($this->columns[$column])
            ? $column
            : null;
    }
}