<?php

/**
* APP 目录
*/
define('APP_PATH',dirname(__DIR__).'/');

/**
 * 定义环境
 */
define('ENVIRONMENT','development');
//define('ENVIRONMENT','production');


/**
 * 加载框架
 */
require __DIR__.'/../../support/Application.php';

/**
 * 启动框架
 */
\support\Application::init(
    ( require APP_PATH.'config/config.php')
);

/**
 * 运行框架
 */
App::run();