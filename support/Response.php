<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 22:34
 */

namespace support;

/**
 * Class Response
 * @package support
 */
class Response extends Object
{

    /**
     * @var
     */
    protected $content;

    protected $version;

    protected $httpStatusViewFile = '&support/resource/HttpStatus.php';

    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];


    public function init()
    {
        if ($this->version === null) {
            if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                $this->version = '1.0';
            } else {
                $this->version = '1.1';
            }
        }
    }

    /**
     * @param $content
     */
    public function setContent( $content )
    {
        $this->content = $content;
    }

    /**
     *
     */
    public function sendContent()
    {
        if(is_array($this->content)){
            //TODO::  一般是 JSON
            print_r($this->content);
        }else{
            echo $this->content;
        }
    }

    public function setHead()
    {
        $this->sendStatsCode();
        header("Content-type: text/html; charset=utf-8");
    }


    /**
     *
     */
    public function send()
    {
        if( $this->content instanceof \Closure){
            echo call_user_func($this->content,$this);
        }else{
            $this->setHead();
            $this->sendContent();
        }
    }

    /**
     *
     */
    public function sendNotFoundFiles()
    {
        $this->sendStatsCode(404);
        $this->renderHttpStatus(404);
        exit;
    }

    public function sendNotAccessPermission()
    {
        $this->sendStatsCode(401);
        $this->renderHttpStatus(401);
        exit;
    }

    public function sendException()
    {
        $this->sendStatsCode(500);
        $this->renderHttpStatus(500);
        exit;
    }

    /**
     * @param $url
     * @param int $statusCode
     */
    public function redirect($url,$statusCode = 302)
    {
        $this->sendStatsCode($statusCode);
        header("Location: {$url}");
        exit();
    }

    /**
     * @param int $statusCode
     */
    public function sendStatsCode( $statusCode = 200 )
    {
        $statusText = static::$httpStatuses[$statusCode];
        header("HTTP/{$this->version} {$statusCode} {$statusText}");
    }

    /**
     *
     * @param $httpStatus
     */
    protected function renderHttpStatus( $httpStatus )
    {
        if(!class_exists( 'support\View' ,false) ){
            require (__DIR__.'/View.php');
        }

        $view = new View();

        echo $view->render(  Application::alias( $this->httpStatusViewFile ),
            [
                'httpStatus' =>$httpStatus ,
                'httpText' => static::$httpStatuses[$httpStatus]
            ]
        );
    }

}