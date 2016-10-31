<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 21:48
 */

namespace support;

use App;

/**
 * Class Router
 * @package support
 */
class Router extends Object
{

    /**
     * @var
     */
    public $control;

    /**
     * @var
     */
    public $action;

    /**
     * @var
     */
    public $controlNamespace ='app\control';

    /**
     * @var string
     */
    protected $accessAuth = 'support\router\AccessAuth';


    /**
     * @var
     */
    public $controlClass;


    public function init()
    {
        if( $this->accessAuth ){
            $this->accessAuth = new $this->accessAuth([
                'router' => $this
            ]);
        }
    }

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function handler( $request  )
    {
        if( $this->match($request->getHttpPath()) ){
            $this->access();
            $control = new $this->controlClass();
            if( method_exists( $control ,$this->action.'Action' ) ){
                //TODO:: 
                Application::User()->logUserAction( $this->getRoute() );
                return call_user_func_array([$control,$this->action.'Action'],[]);
            }else{
                if( App::envInProduction() ){
                    \Response::sendNotFoundFiles();
                }else{
                    throw new \Exception('Not Found Action '.$this->action.'Action' );
                }
            }
        }
        //TODO::
        if( App::envInProduction() ){
            \Response::sendNotFoundFiles();
        }else{
            throw new \Exception('Not Found Page '.$this->controlClass);
        }
    }
    

    /**
     * @param $path
     * @return mixed
     */
    protected function match( $path )
    {
        $info = explode('/',$path);
        
        $this->control = isset($info[0]) && !empty($info[0]) ? ucfirst(strtolower( $info[0] )) : 'Index';
        $this->action  = isset($info[1]) && !empty($info[1]) ? ucfirst( $info[1] )  : 'default';

        $this->controlClass = $this->controlNamespace.'\\'.$this->control.'Control';

        return  class_exists( $this->controlClass );
    }


    protected function access()
    {
        if( ! $this->accessAuth->allowGuest() ){
            //判断用户是否已经授权
            Application::User()->accountAuth();

            if( ! $this->accessAuth->validAllow() ){
                if( App::envInProduction() ){
                    // 无权访问
                    \Response::sendNotAccessPermission();
                }else{
                    //不符合访问条件
                    throw new \Exception('Not Access Permission');
                }
            }
        }
    }


    /**
     * @param $route
     * @return string
     */
    public function route2Url( $route )
    {
        return '/'.str_replace('@','/',$route);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->control.'@'.$this->action;
    }
}