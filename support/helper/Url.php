<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/17
 * Time: 11:52
 */

namespace support\helper;

class Url
{
    /**
     * 创建URL
     * @param $patten
     * @param $get
     */
    public function createUrl($patten,$get)
    {

    }


    /**
     * @param array $param
     * @param bool $append
     * @param bool $needRouter
     * @return string
     */
    public static function setGET( $param = [] ,$append = false,$needRouter = true)
    {
        $GET = $append ? \Request::all('get') : [];
        $GET = array_merge($GET,$param);

        $queryString = $GET ?  '?'.http_build_query($GET) : '';

        if( $needRouter ){
            return '/'.\Request::getHttpPath().$queryString;
        }else{
            return $queryString;
        }

    }
}