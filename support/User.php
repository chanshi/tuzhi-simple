<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/24
 * Time: 09:55
 */

namespace support;

use support\model\Model;

/**
 *
 * 简单处理
 *
 *
 * Class User
 * @package support
 */
class User extends Model
{

    const GUEST_USER = 0;
    const AUTH_USER  = 1;

    /**
     * @var int 角色ID
     */
    protected $roleId = 0 ;

    /**
     * @var int 用户ID
     */
    protected $userId = 0;

    /**
     * @var int 账户状态
     */
    protected $accountStatus = User::GUEST_USER;

    /**
     * @var string
     */
    public $accountGateway = 'User@Login';

    /**
     * @var
     */
    public $AccountAuth = 'support\auth\AccountAuth';
    

    /**
     *
     */
    public function init()
    {
        parent::init();

        if( $this->AccountAuth ) {
            /**
             * 账户
             */
            $this->AccountAuth = new $this->AccountAuth();
            /**
             * 账户检查
             */
            $this->accountStatus = $this->AccountAuth->verifyToken()
                ? User::AUTH_USER
                : User::GUEST_USER;
            /**
             * 账户初始化(如果是验证状态)
             */
            if( $this->accountStatus ){
                $info = $this->AccountAuth->AccountInfo();
                if( empty($info) ) {
                    $this->accountStatus = User::GUEST_USER;
                    $this->AccountAuth->removeToken();
                }else{
                    $this->setAttributes( $info );
                    $this->initUser();
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @return int
     */
    public function getAccountStatus()
    {
        return $this->accountStatus;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function AccountAuth()
    {
        if( $this->accountStatus != User::AUTH_USER ){
            Application::Response()->redirect( Application::Router()->route2Url( $this->accountGateway ) );
        }
        return true;
    }

    public function validAccount( $userName ,$passord )
    {
        if( $this->AccountAuth->verifyAccount($userName,$passord) ){
            //

            $this->setAttributes( $this->AccountAuth->AccountInfo() );

            $this->AccountAuth->createToken();

            $this->initUser();
            return true;
        }
        return false;
    }

    public function validPassword( $userId ,$token )
    {
        if( $this->AccountAuth->verifyPassword( $userId,$token ) ){
            //

            $this->setAttributes( $this->AccountAuth->AccountInfo() );

            $this->AccountAuth->createToken();

            $this->initUser();
            return true;
        }
        return false;
    }

    public function initUser(){}

    public function logUserAction( $route ){
        return null;
    }

    public function logout()
    {
        $this->AccountAuth->removeToken();
    }

}