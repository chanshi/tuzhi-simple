<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 22:26
 */

namespace support\memcached;

use support\Object;
use Config;

class Connection extends Object
{

    /**
     * @var
     */
    public $memcached;

    /**
     * @var
     */
    public $server;

    /**
     * @var
     */
    public $option =
        [
            [ \Memcached::OPT_BINARY_PROTOCOL , TRUE ]
        ];

    /**
     * @var string
     */
    public $command = 'support\memcached\Memcached';

    /**
     * @var string
     */
    protected $memcachedClass = 'Memcached';

    /**
     * @var array
     */
    protected $persistentId = [];


    /**
     *
     */
    public function init()
    {
        if( $this->server ){
            $server = [];
            if( is_array( $this->server ) ){
                foreach( $this->server as $item ){
                    if( is_string($item) && (strpos($item,'@') === 0 ) ){
                        $item = Config::get( $item );
                    }
                    $ser = new Server($item);
                    $this->persistentId[] = $ser->getId();
                    array_push($server,$ser);
                }
            }else{
                if( is_string($this->server) && (strpos($this->server,'@') === 0 ) ){
                    $this->server = Config::get( $this->server );
                }
                $ser = new Server($this->server);
                $this->persistentId[] = $ser->getId();
                array_push($server,$ser);
            }

            $this->server = $server;
        }
    }

    /**
     * @return array|null
     */
    protected function getPersistent()
    {
        $persistent = $this->persistentId;
        if( empty($persistent) ){
            return null;
        }
        if( is_array( $persistent ) ){
            sort($persistent);
            $this->persistentId = md5( join('.',$persistent) );
        }
        return $this->persistentId;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function open()
    {
        if( $this->memcached ){
            return true;
        }

        try{

            $memcached = new $this->memcachedClass( $this->getPersistent() );

            // 配置参数
            foreach($this->option as $option){
                call_user_func_array([$memcached,'setOption'],$option);
            }
            // 添加服务器
            foreach( $this->server as $server ){
                call_user_func_array( [$memcached,'addServer'] ,$server->getArray() );
            }
            // 如果未设置 则使用默认服务器
            if( ! $memcached ){
                throw new \Exception('Cant Create Memcached');
            }
            $this->memcached = $memcached;

        }catch(\Exception $e){
            throw new \Exception('Not Create Memcached Servers ');
        }
    }

    /**
     * @return mixed
     */
    public function getMemcached()
    {
        if( $this->memcached == null ){
            $this->open();
        }
        if( $this->memcached instanceof $this->memcachedClass){
            return $this->memcached;
        }
        return null;
    }

    /**
     * @return string|Memcached
     */
    public function getCommand()
    {
        if( is_string($this->command) ){

            $command = new Memcached(
                [
                    'memcached' =>$this->getMemcached()
                ]
            );

            $this->command = $command;
        }
        return $this->command;
    }


    /**
     * 关闭实例
     */
    public function close()
    {
        if( $this->memcached instanceof $this->memcachedClass){
            $this->memcached->quit();
        }
        $this->memcached = null;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }
}