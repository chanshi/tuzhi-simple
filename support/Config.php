<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 18:01
 */

namespace support;


class Config extends Object
{

    public static $config = [];

    public $path;

    public $loadFile;

    public function __construct( $config )
    {
        if(!isset($config['config'])) return;
        parent::__construct($config['config']);

    }


    public function init()
    {
        foreach( $this->loadFile as $file ){
            if( file_exists($file) ){
                $this->loadFileConfig( $file );
            }else{
                $this->loadFileConfig( $this->path.$file );
            }
        }
    }

    protected function loadFileConfig( $file )
    {
        if( file_exists($file) ){
            try{
                $config = include $file ;
            }catch(\Exception $e){
                throw $e;
            }
            static::$config = array_merge(static::$config , $config);
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @return null|string
     */
    public function get( $key )
    {
        $key = ltrim($key,'@');
        $config =  static::$config ;
        if( strpos($key,'.') !==false ){
            foreach(explode('.',$key) as $item ){
                if( ! isset($config[$item]) ){
                    return NULL;
                }
                $config = $config[$item];
            }
            return $config;
        }else{
            return isset($config[$key])
                ? $config[$key]
                : NULL;
        }
    }

    /**
     *
     * server.goods.abcd
     *
     * value 如果含有 @ 则 获取信息
     * @param $key
     * @param null $value
     * @return $this
     */
    public function set($key,$value = null)
    {
        if( strpos($key,'.') != false ){
            $config =  static::$config;
            foreach( explode('.',$key) as $item ){
                if( ! isset($config[$item]) ){
                    $config[$item] =[];
                }
                $config = $config[$item];
            }
            $config = $value;
        }else{
            static::$config[$key] = $value;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasKey( $key )
    {
        return $this->get( $key ) === NULL
            ? false
            : true;
    }
}