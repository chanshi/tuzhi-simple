<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 22:15
 */

namespace support\database\query;


class Expression
{

    /**
     * @var
     */
    public $expression;

    /**
     * Expression constructor.
     * @param $expression
     */
    public function __construct( $expression )
    {
        $this->expression = $expression;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->expression;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->expression;
    }
}