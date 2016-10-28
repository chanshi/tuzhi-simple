<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 21:23
 */

namespace support;

/**
 *
 * Class Application
 * @package support
 */
class Application
{

    /**
     * @var null
     */
    public static $app = null;

    /**
     * @var
     */
    protected static $servers;

    /**
     * @var array
     */
    protected static $namespace =
        [
            'support' => __DIR__
        ];

    /**
     * @var array
     */
    protected static $loadedClass = [];

    /**
     * @var array
     */
    protected static $alias =
        [
            '&support' => __DIR__
        ];

    /**
     * @var string
     */
    public $timezone ='PRC';

    /**
     * @var string
     */
    public $charset ='utf-8';

    /**
     * @var array
     */
    public $server = [];

    /**
     * Application constructor.
     * @param $config
     */
    public function __construct( $config )
    {
        static::$app = $this;

        if( $config ){
            foreach( $config as $key=> $value ){
                $this->{$key} = $value;
            }
        }

        $this->boot();
    }

    /**
     * App boot
     */
    public function boot()
    {
        date_default_timezone_set( $this->timezone );
        mb_internal_encoding( $this->charset );
        error_reporting(E_ALL);
        $this->loadCore();
        $this->loadFacade();
        $this->loadServers();
    }

    /**
     *
     * @param $config
     */
    public static function init( $config )
    {
        //new static();

        spl_autoload_register(['support\Application','autoload'] , true ,true);

        static::registerServer('config',new Config( $config ));

        $alias = static::config()->get('alias');
        if( $alias ){
            static::$alias = array_merge( static::$alias ,  $alias );
        }

        $namespace = static::config()->get('namespace');
        if($namespace)
        {
            static::$namespace = array_merge(static::$namespace ,$namespace);
        }

        new static( static::config()->get('app') );
    }

    /**
     * @param $aliasName
     * @param null $aliasPath
     * @return mixed|null|string
     */
    public static function alias ( $aliasName , $aliasPath = null )
    {
        if( $aliasPath == null ){
            if( strpos($aliasName ,'&') === 0 ){
                if( ($pos = strpos($aliasName,'/')) > 0 ) {
                    $alias = substr($aliasName,0,$pos);
                    return str_replace(rtrim($alias,'/') , static::$alias[$alias], $aliasName );
                }else {
                    return isset(static::$alias[$aliasName])
                        ? static::$alias[$aliasName]
                        : null;
                }
            }else{
                return $aliasName;
            }
        }else{
            if(is_string($aliasName) && is_dir($aliasPath) ){
                $aliasName = '&' . ltrim($aliasName,'&');
                $aliasPath = rtrim($aliasPath,'/').'/';
                static::$alias[$aliasName] = $aliasPath;
            }
        }
    }


    /**
     * @param $namespace
     * @return mixed
     * @throws \Exception
     */
    public static function getNamespace( $namespace )
    {
        if( isset(static::$namespace[$namespace]) ){
            return static::$namespace[$namespace];
        }
        throw new \Exception('Not Found Namespace '.$namespace.' ');
    }

    /**
     * @param $className
     * @return bool
     * @throws \Exception
     */
    public static function autoload( $className )
    {
        if(isset( static::$loadedClass[$className] ) ){
            return true;
        }

        if( ( $pos = strpos($className,'\\') ) > 0 ){
            $class = str_replace('\\','/',$className);
            $namespace = substr($class,0,$pos);
            $path = static::getNamespace($namespace);

            $classFile = rtrim($path,'/').'/'.substr($class, $pos+1 ).'.php';

            if( ! file_exists($classFile) ){
                throw new \Exception('Not Found File '.$classFile);
            }

            try{
                include  $classFile;
            }catch(\Exception $e){
                throw new \Exception('PHP ERROR IN CLASS FILE '.$classFile);
            }
            static::$loadedClass[$className] = $classFile;
        }
        return true;
    }


    /**
     * @see FApp
     */
    public static function run() {

        try{

            $content =  static::router()->handler( static::request() );

            static::response()->setContent( $content );
            static::response()->send();

        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public static function loadCore()
    {
        $core = static::core();
        foreach($core as $alias=>$class)
        {
            /**
             *  简单方法 不解决依赖
             */
            static::$servers[$alias] = new $class();
        }
        return true;
    }

    /**
     * @return array
     */
    public static function core()
    {
        return
            [
                'errorHandler' => 'support\Errors',
                'request' => 'support\Request',
                'router' => 'support\Router',
                'response' => 'support\Response',
                'security'=> 'support\Security',
                'log'=>'support\Log'
            ];
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic( $name, $arguments)
    {
        $name = strtolower($name);
        if(in_array( $name , array_keys( static::$servers ) )){
            return static::$servers[$name];
        }
        throw new \Exception('Not Found Method In Application');
    }

    /**
     * @param $name
     * @param $server
     * @return mixed
     */
    public static function registerServer( $name ,$server )
    {
        $name = strtolower($name);
        return static::$servers[$name] = $server;
    }

    /**
     *
     */
    public function loadServers()
    {
        if( $this->server ){
            foreach( $this->server as $name=>$server ){
                /**
                 *  简单方法 不解决依赖
                 */
                static::$servers[$name] = new $server();
            }
        }
    }

    /**
     * 是否生产环境
     * 
     * @return boolean
     */
    public static function envInProduction()
    {
        return defined('ENVIRONMENT') && ENVIRONMENT == 'production'
            ? true
            : false;
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getServer($name)
    {
        $name = strtolower($name);
        return static::$servers[$name];
    }


    public static function hasServer( $name )
    {
        $name = strtolower($name);
        return isset(static::$servers[$name]);
    }


    protected static function loadFacade()
    {
        $filePath = static::alias('&support').'/facade/';
        $facade =
            [
                'App',
                'Request',
                'Response',
                'Router',
                'View',
                'Cache',
                'Config',
                'DB',
                'Control',
                'ActiveRecord',
                'Model',
                'Collection',
                'User',
                'Log'
            ];

        foreach( $facade as $class ){
            if( file_exists( $filePath.$class.'.php' ) ){
                include  $filePath.$class.'.php';
            }
        }
    }
}