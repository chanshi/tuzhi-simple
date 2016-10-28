<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 22:07
 */
namespace support\cache;

use support\Object;

class Cache extends Object
{
    /**
     * @var array
     */
    protected static $map =
        [
            'file' => 'support\cache\drive\File',
            'memcached' => 'support\cache\drive\Memcached'
        ];

    /**
     * @var
     */
    public $default = 'file';

    /**
     * @var array
     */
    public $support = [];


    /**
     * @var array
     */
    protected $instance = [];


    /**
     * @param $support
     * @return mixed
     * @throws \Exception
     */
    public function createInstance( $support )
    {
        if( ! isset( $this->support[$support] )  ){
            throw new \Exception( 'Not Support This Method '.$support );
        }

        return new Cache::$map[$support]( $this->support[$support]  );
    }

    /**
     * @param null $method
     * @return mixed
     * @throws NotSupportException
     */
    public function getInstance( $method = null )
    {
        $method = $method == null ? $this->default : $method;
        $method = strtolower($method);
        if( ! isset($this->instance[$method]) ){
            $this->instance[$method] = $this->createInstance($method);
        }
        return $this->instance[$method];
    }


}