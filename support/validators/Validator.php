<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 12:08
 */
namespace support\validators;


use support\Object;

class Validator extends Object
{

    protected $errors;

    protected $rules;


    /**
     * @param $data
     */
    public function valid( $data  )
    {

    }


    /**
     * @param null $attribute
     * @return mixed
     */
    public function getErrors( $attribute = null )
    {
        if($attribute == null){
            return $this->errors;
        }else {
            return isset($this->errors[$attribute])
                ? $this->errors[$attribute]
                : null;
        }
    }

    /**
     * @return mixed
     */
    public function getFirstError()
    {
        $error = $this->errors;
        return array_shift($error);
    }
}