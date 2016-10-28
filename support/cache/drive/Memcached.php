<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 00:08
 */

namespace support\cache\drive;

use support\Object;
use support\cache\ICache;
use support\cache\CacheTrait;

class Memcached extends Object implements ICache
{

    use CacheTrait;

    /**
     * @var 操作实例
     */
    protected $memcached;

    /**
     * @var 服务器 配置参数
     */
    public $server;

    /**
     * @var string  
     */
    public $connection = 'support\memcached\Connection';

    /**
     *
     */
    public function init()
    {
        $connection = new $this->connection(
            [
                'server' =>$this->server
            ]
        );
        $this->memcached = $connection->getCommand();
    }

    /**
     * @param $key
     * @param null $value
     * @param int $expiry
     * @return mixed
     */
    public function set($key, $value = null, $expiry = 0)
    {
        return $this->memcached->set(
            $this->getKey($key),
            $value,
            $expiry == 0 ? 0 : time() + $expiry
        );
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $result = $this->memcached->get(  $this->getKey($key) );
        return $result
            ? $result
            : null;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        return $this->memcached->delete(
            $this->getKey($key)
        );
    }

    /**
     * @return mixed
     */
    public function flush()
    {
        return $this->memcached->flush();
    }

    /**
     * @param $key
     * @param int $step
     * @param int $expiry
     * @return mixed
     */
    public function increment($key, $step = 1,$expiry = 0)
    {
        return (int) $this->memcached->increment(
            $this->getKey($key),
            $step,
            null,
            $expiry == 0 ? 0 : time() + $expiry
        );
    }

    /**
     * @param $key
     * @param int $step
     * @param int $expiry
     * @return mixed
     */
    public function decrement($key, $step = 1,$expiry = 0)
    {
        return (int) $this->memcached->decrement(
            $this->getKey($key),
            $step,
            null,
            $expiry == 0 ? 0 : time() + $expiry
        );
    }
}