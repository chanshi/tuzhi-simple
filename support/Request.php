<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 21:37
 */

namespace support;

class Request extends Object
{

    protected $httpSecure;

    protected $httpDomain;

    protected $httpPort;

    protected $httpUri;

    protected $httpPath;

    protected $httpQueryString;

    protected $httpScript;

    protected $httpHost;

    protected $rawBody;

    protected $cookie;

    public function init()
    {
        session_start();
        $this->cookie = new Cookie();
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        if($this->httpSecure == null ){
            $this->httpSecure = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on') === 0 || $_SERVER['HTTPS'] == 1 )
                || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') === 0;
        }
        return $this->httpSecure;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        if( $this->httpDomain == null ){
            if( isset($_SERVER['HTTP_HOST']) ){
                $this->httpDomain = $_SERVER['HTTP_HOST'];
            }else if( isset($_SERVER['SERVER_NAME']) ){
                $this->httpDomain = $_SERVER['SERVER_NAME'];
            }
        }
        return $this->httpDomain;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        if($this->httpPort == null){
            $this->httpPort = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : 80;
        }
        return $this->httpPort;
    }

    /**
     * @return string
     */
    public function getHttpHost()
    {
        if( $this->httpHost == null ){
            $this->httpHost = ($this->isSecure() ? 'https://' : 'http://')
                .  $this->getDomain()
                .( $this->getPort() == 80  ? '' : ':'.$this->getPort()  );
        }
        return $this->httpHost;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getHttpUri()
    {
        if($this->httpUri == null)
        {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
                $this->httpUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $this->httpUri = $_SERVER['REQUEST_URI'];
                if ($this->httpUri !== '' && $this->httpUri[0] !== '/') {
                    $this->httpUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->httpUri);
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
                $this->httpUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $this->httpUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                throw new \Exception('Cant found URI');
            }
        }
        return $this->httpUri;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getHttpPath()
    {
        if($this->httpPath == null){
            $pathInfo = $this->getHttpUri();
            if( ($pos = strpos($pathInfo,'?')) !== false ){
                $pathInfo = substr($pathInfo,0,$pos);
            }
            $pathInfo = urldecode($pathInfo);

            $scriptUrl = $this->getHttpScript();
            $baseUrl = $this->getHttpBaseUrl();

            if (strpos($pathInfo, $scriptUrl) === 0) {
                $pathInfo = substr($pathInfo, strlen($scriptUrl));
            } elseif ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0) {
                $pathInfo = substr($pathInfo, strlen($baseUrl));
            } elseif (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0) {
                $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
            }

            if(substr($pathInfo,0,1) === '/'){
                $pathInfo = substr($pathInfo,1);
            }
            $this->httpPath = $pathInfo;
        }
        return $this->httpPath;
    }

    /**
     * @return string
     */
    public function getHttpQueryString()
    {
        if($this->httpQueryString == null)
        {
            $this->httpQueryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        }
        return $this->httpQueryString;
    }

    /**
     * @return string
     */
    public function getHttpScript()
    {
        if($this->httpScript == null){
            $scriptUrl = '';
            $scriptFile = isset( $_SERVER['SCRIPT_FILENAME'] ) ? $_SERVER['SCRIPT_FILENAME'] : '';
            $scriptName = basename($scriptFile);
            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $scriptUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $scriptName) {
                $scriptUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && ($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                $scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } elseif (!empty($_SERVER['DOCUMENT_ROOT']) && strpos($scriptFile, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $scriptFile));
            }
            $this->httpScript = $scriptUrl;
        }
        return $this->httpScript;
    }

    /**
     * @return mixed
     */
    public function getHttpBaseUrl()
    {
        return rtrim(dirname( $this->getHttpScript()),'\\/');
    }


    /**
     * @param $field
     * @param string $type
     * @param null $default
     * @return mixed
     */
    public  function post($field , $type = 'string',$default = null )
    {
        $result = $default;
        if(array_key_exists( $field ,$_POST )){
            $result = $_POST[$field] ;
        }
        return  $this->forceField( $type ,$result);
    }

    /**
     * @param $field
     * @param string $type
     * @param null $default
     * @return mixed
     */
    public  function get($field ,$type = 'string',$default = null )
    {
        $result = $default;
        if(array_key_exists( $field ,$_GET )){
            $result =  $_GET[$field] ;
        }
        return  $this->forceField( $type ,$result);
    }

    /**
     * @param $type
     * @param $value
     * @return mixed
     */
    protected  function forceField( $type,$value )
    {
        if( is_array($value) ) return $value;
        
        switch ( strtolower($type) ){
            case 'int' : $value = intval( $value);
                break;
            // 添加 安全
            case 'string' :  $value = Application::security()->xssFilter( strval( trim( $value ) ) );
                break;
            case 'bool' : $value = boolval($value);
                break;
        }
        return $value;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function all( $type = 'post'  )
    {
        $result = strtolower($type) == 'post' ? $_POST : $_GET;
        return $result;
    }

    /**
     * @return null
     */
    public function getUserIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * @return mixed
     */
    public function getRawBody()
    {
        if ($this->rawBody === null) {
            $this->rawBody = file_get_contents('php://input');
        }

        return $this->rawBody;
    }

    /**
     * @param $key
     * @param null $value  -1
     * @return null
     */
    public function session( $key , $value = null )
    {
        if($value == null) {
            return isset($_SESSION[$key])
                ? $_SESSION[$key]
                : null;
        }else if($value == -1){
            unset($_SESSION[$key]);
            return true;
        }else{
            $_SESSION[$key] = $value;
        }
    }

    public function cookie()
    {
        return $this->cookie;
    }

}