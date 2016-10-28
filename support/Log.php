<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/6/15
 * Time: 13:51
 */

namespace support;



class Log extends Object
{
    /**
     * 类型
     */
    const LOG_NOTICE  = 1;

    const LOG_WARNING = 2;

    const LOG_ERROR   = 4;

    const LOG_DEBUG   = 8;

    const LOG_PROFILE = 16;

    const LOG_ALL     = 31;

    /**
     * @var array 类型别名
     */
    public static $Type=
        [
            1 => 'notice',
            2 => 'warning',
            4 => 'error' ,
            8 => 'debug',
            16 => 'profile'
        ];

    /**
     * @var int
     */
    public $allow = 4;


    /**
     * @var array
     */
    protected static $logs = [];

    /**
     * @var null
     */
    public $storage = 'support\log\File';


    public function init()
    {
        $this->storage = new $this->storage();
    }


    /**
     * @param $message
     * @param $type
     */
    public function record( $message  , $type )
    {
        if( $type & $this->allow )
        {
            $content = $this->normalizeMessage( $message );
            //
            $this->storage->record( $content , static::$Type[$type] );
            static::$logs[ $type ] = $content;
        }
    }

    /**
     * @param $message
     * @return string
     */
    protected function normalizeMessage( $message )
    {
        return "[".date('H:i:s')."] {$message}";
    }


    /**
     * @return bool
     */
    public function save()
    {
        foreach (static::$logs as $type =>$value){
            $this->storage->record( $value , static::$Type[$type] );
        }
        return true;
    }

    /**
     *
     */
    public function __destruct()
    {
        //$this->save();
    }
}