<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 20:48
 */

namespace support;


class Object
{
    /**
     * Object constructor.
     * @param array $config
     */
    public function __construct( $config = [] )
    {
        if( $config ){
            foreach($config as $key=>$value){
                $this->{$key} = $value;
            }
        }
        $this->init();
    }

    public function init(){}
}