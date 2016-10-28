<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 22:39
 */

namespace support\database\schema;

/**
 * Class ColumnSchema
 * @package support\database\schema
 */
class ColumnSchema
{
    public $name;

    public $type;

    public $size;

    public $isPrimary = false;

    public $allowNull;

    public $defaultValue;

    public $autoIncrement = false;

    public function getDefault()
    {

    }
}