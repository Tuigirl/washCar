<?php
// 检测PHP环境
if(version_compare(PHP_VERSION, '5.3.0','<'))  die('require PHP > 5.3.0 !');

define('DS', DIRECTORY_SEPARATOR);
// 站点目录
define('SITE_DIR', dirname(__FILE__));
// 站点地址
define('SCRIPT_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/\\'));

define('SITE_URL', $_SERVER['HTTP_HOST'] . SCRIPT_DIR);
//来源页面
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
//文件上传根目录

// ThinkPHP定义
define('APP_DEBUG', true);
define('THINK_PATH', SITE_DIR . DS . 'Libs' . DS . 'ThinkPHP' . DS);
define('APP_PATH', SITE_DIR . DS . 'App' . DS);
define('UPLOAD_PATH', SITE_DIR . DS . 'Public' . DS.'upload'.DS);
define('RUNTIME_PATH', SITE_DIR . DS . 'Public' . DS . 'Runtime' . DS);   // 系统运行时目录

//定义网站的web_path
define('G_HTTP_HOST', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
define('G_HTTP', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'https://');
define("WEB_PATH", dirname(G_HTTP . G_HTTP_HOST . $_SERVER['SCRIPT_NAME']));

define('SYS_TIME',time());
define('SYS_DATE',date('Y-m-d H:i:s'));
require(THINK_PATH.'ThinkPHP.php');