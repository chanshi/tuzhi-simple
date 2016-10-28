<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/17
 * Time: 00:11
 */

namespace support\model;

use support\helper\Html;

/**
 *
 *  action = /form/ab-ce-des
 *
 *
 * Class Form
 * @package support\model
 */
class Form extends Model
{
    /**
     * @param array $option
     * @return string
     */
    public function beginForm( $option = [])
    {
        return Html::tag('form',null,$option);
    }

    /**
     * @return string
     */
    public function endForm(){
        return Html::endTag('form');
    }
}