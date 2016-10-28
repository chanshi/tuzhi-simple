<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/6/15
 * Time: 11:52
 */

namespace support;

/**
 * Class Security
 * @package support
 */
class Security extends Object
{
    protected $xss= 'support\Security\XSS';


    public $enableXSS = true;


    public function init(){
        $this->xss = new $this->xss();
    }

    /**
     * @param $string
     * @return mixed
     */
    public function xssFilter( $string )
    {
        if($this->enableXSS){
            return $this->xss->xss_clean($string);
        }else{
            return $string;
        }
    }
}