<?php
// 检测PHP环境
if(version_compare(PHP_VERSION, '5.3.0','<'))  die('require PHP > 5.3.0 !');

define('DS', DIRECTORY_SEPARATOR);
// 站点目录
define('SITE_DIR', dirname(__FILE__));


//文件上传根目录

// ThinkPHP定义
define('APP_DEBUG', true);
define('THINK_PATH', SITE_DIR . DS . 'Libs' . DS . 'ThinkPHP' . DS);
define('APP_PATH', SITE_DIR . DS . 'App' . DS);
define('UPLOAD_PATH', SITE_DIR . DS . 'Public' . DS.'upload'.DS);
define('RUNTIME_PATH', SITE_DIR . DS . 'Public' . DS . 'Runtime' . DS);   // 系统运行时目录
define('APP_MODE','cli');

//定义工作环境
// define('APP_STATUS','office');

define('SYS_TIME',time());
require(THINK_PATH.'ThinkPHP.php');