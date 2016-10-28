<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 21:43
 */

namespace support;

class Control
{

    public function goHome()
    {
        \Response::redirect('/');
    }


}