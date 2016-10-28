<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/6/23
 * Time: 15:59
 */

namespace support;

use support\model\Model;

class Cookie extends Model
{
    /**
     * @param $key
     * @param $value
     * @param int $expired
     */
    public function set( $key,$value,$expired = 3600 )
    {
        $expired = time() + $expired;
        $domain =\Request::getDomain();
        $domain = explode(':',$domain);
        $domain = '.'.$domain[0];
        $secure = \Request::isSecure()? 1:0;
        $httpOnly = true;
        setcookie( $key , $value , $expired ,'/', $domain, $secure, $httpOnly);
    }

    /**
     * 获取COOKIE
     * @param $key
     * @return null
     */
    public function get( $key )
    {
        return isset( $_COOKIE[$key] ) ? $_COOKIE[$key] : null;
    }

    /**
     * 删除COOKIE
     * @param $key
     */
    public function rm( $key )
    {
        return $this->set($key,null,-1);
    }
}