<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/25
 * Time: 10:13
 */

namespace support\router;
use support\Application;
use support\Object;


/**
 * Class Auth
 * @package support\router
 */
class AccessAuth extends Object
{

    /**
     * @var string
     */
    protected $accessTable = 'support\router\AccessTable' ;

    /**
     * @var array
     */
    protected static $maps =
        [
            'user'=>'support\router\auth\User'
        ];

    /**
     * @var array
     */
    protected $authMaps = [];

    /**
     * @var
     */
    public $router;

    /**
     * @var
     */
    protected $access;


    /**
     * init
     */
    public function init()
    {
        if( $this->accessTable){
            $this->accessTable = new $this->accessTable();
            //TODO::  分层 混乱
            $this->accessTable->loadAccess( Application::config()->get('access')  );
        }

        foreach (static::$maps as $name=>$class){
            $this->authMaps[$name] = new $class();
        }
    }

    /**
     * @return mixed
     */
    protected function getAccess()
    {
        if( ! $this->access ){
            $this->access = $this->accessTable->getAccess( $this->router );
        }
        return $this->access;
    }

    /**
     * 简单处理
     */
    public function allowGuest()
    {
        $access = $this->getAccess();

        if( isset($access['deny']) && $access['deny'] == 'guest' ) {
           return false;
        }

        return true;

    }


    /**
     * @return bool false 不允许 true 允许
     */
    public function validAllow()
    {
        $access = $this->getAccess();

        if( isset( $access['user'] ) ){
            return $this->authMaps['user']->match($access['user']);
        }
        return true;
    }

}