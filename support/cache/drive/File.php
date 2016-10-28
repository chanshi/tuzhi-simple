<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 22:00
 */

namespace support\cache\drive;

use support\Object;
use support\cache\ICache;
use support\cache\CacheTrait;
use support\filesystem\FileSystem;
use App;

class File extends Object implements ICache
{

    /**
     * Trait
     */
    use CacheTrait;

    /**
     * @var string
     */
    public $cacheDir  = '&runtime/cache';

    /**
     * @var string
     */
    public $fileSuffix = '.cache';

    /**
     * @var null
     */
    protected $FileSystem = null;

    /**
     *
     */
    public function init()
    {
        $this->FileSystem = new FileSystem();
        $this->cacheDir =  rtrim( App::alias($this->cacheDir),'/' ).'/';
    }

    /**
     * @param $key
     * @return string
     */
    public function getPathFile( $key )
    {
        $fullKey = md5( $this->getKey($key) );
        $path = $this->cacheDir.substr($fullKey,0,2).'/'.substr($fullKey,2,2).'/';
        if( !is_dir($path) ){
            \support\helper\File::createDirection($path,0755,true);
        }
        $file = $path.substr($fullKey,4).$this->fileSuffix;
        return $file;
    }

    /**
     * @param $key
     * @param null $value
     * @param int $expiry
     * @return mixed
     */
    public function set($key, $value = null, $expiry = 0)
    {
        $this->delete($key);

        $content['expiry']  = $expiry == 0 ? 0 : time() + $expiry;
        $content['content'] = $value;

        return $this->FileSystem->write(
            $this->getPathFile($key) ,
            $this->setContent( $content )
        );
    }

    /**
     * @param $key
     * @return null
     */
    public function get($key)
    {
        $result = $this->FileSystem->read( $this->getPathFile($key) );
        if($result){
            $content = $this->getContent($result);
            if( $content['expiry'] != 0 &&  $content['expiry'] < time() ){
                $this->delete($key);
                return null;
            }else {
                return $content['content'];
            }
        }else{
            return null;
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        return $this->FileSystem->rm(
            $this->getPathFile($key)
        );
    }

    /**
     * 清空文件夹
     */
    public function flush()
    {
        \support\helper\File::clearDir($this->cacheDir , true);
    }

    /**
     * @param $key
     * @param int $step
     * @param int $expiry
     * @return int|null
     */
    public function increment($key, $step = 1,$expiry = 0)
    {
        $content = $this->get($key);

        $value = $content == null ? $step : ($content + $step );

        $this->set( $key , $value ,$expiry);

        return $value;
    }

    /**
     * @param $key
     * @param int $step
     * @param int $expiry
     * @return int
     */
    public function decrement($key, $step = 1,$expiry = 0)
    {
        $content = $this->get($key);

        $value = $content == null ? 0 : max(0,$content - $step);

        $this->set( $key , $value ,$expiry);

        return $value;
    }
}