<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 23:19
 */

namespace support;

use App;
use support\view\ViewBlock;

class View extends Object
{
    use ViewBlock;

    /**
     * @var
     */
    public $viewPath;

    /**
     * @var
     */
    public $layoutPath;

    /**
     * @var string
     */
    protected $layoutName = 'main';
    
    


    public function init()
    {
        $this->viewPath = rtrim( App::alias( $this->viewPath ) ,'/' ).'/';
        $this->layoutPath = rtrim (App::alias($this->layoutPath) ,'/' ).'/';
    }


    /**
     * @param $view
     * @param array $_param_
     * @return mixed
     * @throws \Exception
     */
    public function fetch( $view ,$_param_ = [] )
    {
        $file = $this->viewPath.$view.'.php';
        return $this->phpEngine( $file , $_param_ );
    }

    /**
     * @param $view
     * @param array $_param_
     * @return mixed
     * @throws \Exception
     */
    public function layout( $view ,$_param_ = [])
    {
        $_param_['content'] = $this->fetch($view,$_param_);
        $layoutFile = $this->layoutPath.$this->layoutName.'.php';
        return $this->phpEngine($layoutFile ,$_param_);
    }

    /**
     * @param $file
     * @param array $_param_
     * @return mixed
     * @throws \Exception
     */
    public function render( $file ,$_param_= [] ){
        return $this->phpEngine($file ,$_param_);
    }
    
    /**
     * 解析器
     * @param $viewFile
     * @param array $_param_
     * @return mixed
     * @throws \Exception
     */
    private function phpEngine( $viewFile ,$_param_ =[] )
    {
        $level = ob_get_level();

        ob_start();

        extract($_param_ ,EXTR_OVERWRITE);

        try{
            include  $viewFile;
        }catch(\Exception $e){
            while( ob_get_clean() > $level){
                ob_get_clean();
            }
            throw $e;
        }
        return trim( ob_get_clean() );
    }

}