<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/7/12
 * Time: 17:34
 */

namespace support\view;

/**
 * Class ViewBlock
 * @package support\view
 */
trait ViewBlock
{


    protected $TITLE ='';

    /**
     * @var
     */
    protected $HTML_HEAD = '';

    /**
     * @var
     */
    protected $HTML_BODY_STAR = '';

    /**
     * @var
     */
    protected $HTML_BODY_END = '';

    /**
     * @return null
     */
    public static function begin()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @param null $content
     * @return string
     */
    public static function end( & $content = null )
    {
        $block = ob_get_clean();
        return  $content = $block;
    }
}