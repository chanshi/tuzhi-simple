<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/25
 * Time: 10:32
 */

namespace support\router\auth;


use support\Application;

class User
{

    public $error;

    /**
     * @param $access
     * @return bool
     */
    public function match( $access )
    {
        $authStatus = true;
        foreach( $access as $validType => $validParam ){
            switch( strtolower( $validType ) ){
                case 'account' :
                    $authStatus = $authStatus && $this->validAccountStatus($validParam);
                    $this->error = ! $authStatus
                        ? 'account'
                        : null;
                    break;
                case 'role' :
                    $authStatus = $authStatus && $this->validRoleId($validParam);
                    $this->error = ! $authStatus
                        ? 'role'
                        : null;
                    break;
                case 'id' :
                    $authStatus = $authStatus && $this->validUserId($validParam);
                    $this->error = ! $authStatus
                        ? 'Id'
                        : null;
                    break;
            }
        }
        return $authStatus;
    }

    /**
     * @param $Type
     * @return bool
     */
    public function validAccountStatus(  $Type  )
    {
        $userType =  Application::User()->getAccountStatus();
        return is_array($Type)
            ? in_array($userType,$Type)
            : $userType == $Type;

    }

    /**
     * @param $roleId
     * @return bool
     */
    public function validRoleId( $roleId )
    {
        $userRoleId = Application::User()->getRoleId();
        return is_array($roleId)
            ? in_array($userRoleId, $roleId)
            : $userRoleId == $roleId;

    }

    /**
     * @param $id
     * @return bool
     */
    public function validUserId( $id )
    {
        $userId = Application::User()->getUserId();
        return is_array($id)
            ? in_array($userId, $id)
            : $userId == $id;

    }
}