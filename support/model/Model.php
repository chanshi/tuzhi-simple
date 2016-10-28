<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 09:57
 */

namespace support\model;

use support\Object;

/**
 * Class Model
 * @package support\model
 */
class Model extends Object implements \Countable ,\ArrayAccess ,\IteratorAggregate
{

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $oldAttributes = [];

    /**
     * @var array
     */
    protected $attLabel = [];

    /**
     * @var array
     */
    protected $attFormat = [];

    /**
     * @var array
     */
    protected $attRules = [];

    /**
     * @var array
     */
    protected $attMaps = [];

    /**
     * @var array
     */
    protected $attFilter = [];

    protected $denyAtt = [];

    /**
     * @var string
     */
    //public $validClass  = 'support\validators\Validator';
    public $validClass  = 'support\ValidData';

    /**
     * @var null
     */
    protected $validator = null ;

    /**
     * @return mixed
     */
    public function init()
    {
        $this->attRules = $this->rules();
        $this->attLabel = $this->labels();
        $this->attFormat = $this->format();
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function setAttributes( $attributes )
    {
        foreach($attributes as $attribute=>$value ){
            $this->setAttribute($attribute,$value);
        }
        return true;
    }

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function setAttribute( $attribute ,$value )
    {

        if( $this->filterAttribute($attribute) == false ){
            return false;
        }

        if( $this->denyAttribute($attribute) ){
            return false;
        }

        return $this->attributes[$attribute] = $value;
    }

    /**
     * @param $attribute
     * @return bool
     */
    protected function filterAttribute($attribute)
    {
        if(!empty($this->attFilter) && ! in_array($attribute ,$this->attFilter) ){
            return false;
        }
        return true;
    }

    /**
     * @param $attribute
     * @return bool
     */
    protected function denyAttribute( $attribute )
    {
        if( !empty($this->denyAtt) && in_array($attribute,$this->denyAtt) ){
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @return mixed|null
     */
    public function getAttribute( $attribute )
    {
        return isset($this->attributes[$attribute])
            ? $this->attributes[$attribute]
            : null;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getLabel( $attribute )
    {
        if( isset($this->attLabel[$attribute]) )
        {
            return $this->attLabel[$attribute];
        }
        return $attribute;
    }

    /**
     * @param $attribute
     * @return mixed|null
     */
    public function getFormatAtt($attribute)
    {
        if(isset($this->attFormat[$attribute])){
            return call_user_func($this->attFormat[$attribute] ,$this->getAttribute($attribute));
        }
        return $this->getAttribute($attribute);
    }

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function __set($attribute ,$value)
    {
        return $this->setAttribute($attribute,$value);
    }

    /**
     * @param $attribute
     * @return mixed|null
     */
    public function __get($attribute)
    {
        return $this->getAttribute($attribute);
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return count($this->attributes);
    }

    /**
     * @param mixed $attribute
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet($attribute ,$value)
    {
        return $this->setAttribute($attribute,$value);
    }

    /**
     * @param mixed $attribute
     * @return mixed|null
     */
    public function offsetGet($attribute)
    {
        return $this->getAttribute($attribute);
    }

    /**
     * @param mixed $attribute
     * @return bool
     */
    public function offsetExists($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * @param mixed $attribute
     */
    public function offsetUnset($attribute)
    {
        if(isset($this->attributes[$attribute])){
            unset($this->attributes[$attribute]);
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this );
    }

    /**
     * @return mixed
     */
    protected function labels()
    {
        return [];
    }

    /**
     * @return mixed
     */
    protected function rules()
    {
        return [];
    }

    /**
     * @return mixed
     */
    protected function format()
    {
        return [];
    }

    /**
     * @return mixed
     */
    protected function getValidator()
    {
        if($this->validator == null){
            $this->validator = new $this->validClass( $this );
            // 设置 Rule;
            $this->validator->setRules( $this->rules() );
        }
        return $this->validator;
    }


    public function verify( $data = null )
    {
        if( $data == null ){
            $data = $this->attributes;
        }

        return $this->getValidator()->valid( $data );
    }

    /**
     * @param null $attribute
     * @return mixed
     */
    public function getErrors( $attribute = null )
    {
        return $this->getValidator()->getErrors($attribute);
    }

    public function getFirstError()
    {
        return $this->getValidator()->getFirstError();
    }

}