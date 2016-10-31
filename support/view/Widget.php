<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 23:40
 */

namespace support\view;

class Widget
{
    /**
     * @var
     */
    public $html;

    /**
     *
     */
    public static function begin(){}

    /**
     *
     */
    public static function end(){}

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->html;
    }
}