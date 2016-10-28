<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/27
 * Time: 11:24
 */

namespace support\helper;

class Str
{

    public static function random( $num = 6 )
    {
        $all = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789';
        return substr(str_shuffle($all),0,$num);
    }

    public static function toMoneyStr( $str )
    {
        return sprintf('%.2f',$str);
    }
}