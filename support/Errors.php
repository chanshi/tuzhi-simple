<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 20:34
 */

namespace support;

class Errors extends Object
{

    public $exception;

    public $errorView = '&support/resource/exception.php';


    public function init()
    {
        ini_set('display_errors', false);
        // 基本 错误处理
        set_error_handler([$this,'handlerError']);
        // 异常处理
        set_exception_handler([$this,'handlerException']);
        // 定义 最高级别错误 处理函数
        register_shutdown_function([$this,'handlerFatalError']);
    }

    protected function loadExceptionFile()
    {
        if( !class_exists('support\ErrorException',false) ){
            require( __DIR__.'/ErrorException.php');
        }
    }

    public function unRegister()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    public function handlerError($code ,$message ,$file ,$line)
    {
        $this->loadExceptionFile();

        if( error_reporting() & $code ){

            $exception = new ErrorException($message,$code,$code,$file,$line);
            throw $exception;
        }
        return false;
    }

    public function handlerException( $exception )
    {$this->exception = $exception;

        // 禁止 递归捕获
        $this->unRegister();

        try{

            $this->clearOutput();
            $this->renderException( $exception );

        }catch( \Exception $e ){
            $msg  = "异常处理时发生另外一个错误\n";
            $msg .= (string) $e;
            $msg .= "\n处理异常为:\n";
            $msg .= (string) $exception;

            error_log($msg);
            //处理显示
            echo $msg;

            exit(1);
        }
        $this->exception = null;

    }

    public function clearOutput()
    {
        for($level = ob_get_level() ; $level > 0 ;$level-- ){
            if( ! @ob_end_clean() ){
                ob_clean();
            }
        }
        return true;
    }

    public function handlerFatalError()
    {
        $this->loadExceptionFile();

        $error = error_get_last();

        if( $this->isFatalError($error) ){
            $exception = new ErrorException($error['message'],$error['type'] ,$error['type'] ,$error['file'] ,$error['line']);
            $this->clearOutput();
            $this->renderException( $exception );
            exit(1);
        }
    }

    /**
     * @param $error
     * @return bool
     */
    public static function isFatalError($error)
    {
        return isset($error['type']) &&
        in_array($error['type'],
            [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_CORE_WARNING,
                E_COMPILE_ERROR,
                E_COMPILE_WARNING
            ]);
    }

    /**
     * @param $exception
     */
    public function renderException( $exception )
    {

        // 加入环境
        if( Application::envInProduction() ){

            Application::log()->record($exception,Log::LOG_ERROR);

            if( Application::hasServer('response') ){
                Application::response()->sendException();
            }else{
                exit('you has an exception');
            }
        }else{

            if(!class_exists( 'support\View' ,false) ){
                require (__DIR__.'/View.php');
            }

            $view = new View();

            echo $view->render(  Application::alias( $this->errorView ), [ 'exception' =>$exception ] );
        }
    }
}