<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/24
 * Time: 10:16
 */

namespace support\auth;
use support\Object;

/**
 * Class AccountAuth
 * @package support\auth
 */
class AccountAuth extends Object
{
    /**
     * @var string 授权入口
     */
    public $gateway = 'public@login';

    /**
     * @var string
     */
    public $intruderCode = '';
    


    /**
     *  验证 Token
     */
    public function verifyToken(  )
    {
        //todo::
        return true;
    }

    public function verifyPassword( $userId ,$password )
    {
        return true;
    }

    /**
     * @param $userName
     * @param $password
     * @return boolean
     */
    public function verifyAccount( $userName , $password )
    {
        //todo::
        return true;
    }

    public function removeToken(){}


    /**
     * 创建
     */
    public function createToken()
    {
        //todo::
        return true;
    }

    public function AccountInfo()
    {
        return [];
    }

}