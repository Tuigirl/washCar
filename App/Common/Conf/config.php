<?php
defined('THINK_PATH') or exit();
return array(
    'LOAD_EXT_CONFIG' => 'config_weixin,config_cache,config_mailer,config_outRequest',
    //'配置项'=>'配置值'
    'DB_FIELDS_CACHE' => false,        // 启用字段缓存
    'SHOW_PAGE_TRACE' => false,        //调试配置
    'APP_USE_NAMESPACE' => true,
    'DEFAULT_MODULE' => 'Caryu',
    'MODULE_DENY_LIST' => array('Common', 'Runtime'),
    'MODULE_ALLOW_LIST' => array('Caryu', 'Xiaochengxu','Wechat'),    //允许访问的文件目录

    /* 数据库设置 */
    'DB_TYPE' => 'mysqli',     // 数据库类型
    'DB_HOST' => '127.0.0.1',  // 服务器地址
    'DB_NAME' => 'xiaochengxu',        // 数据库名
    'DB_USER' => 'root',       // 用户名
    'DB_PWD' => 'DekZYaqo5kEJPDkeji5566',           // 密码 //DekZYaqo5kEJPDkeji5566
    'DB_PORT' => '3306',       // 端口
    'DB_PREFIX' => '',      // 数据库表前缀

    /* URL配置 */
    'URL_MODEL' => 0,           // URL模式
    'URL_PATHINFO_DEPR' => '/',         // PATHINFO URL分割符
    'URL_ROUTER_ON' => false,       // 是否开启URL路由
    'URL_ROUTE_RULES' => array(),     // 默认路由规则 针对模块


    'TMPL_L_DELIM' => '<{',        // 模板引擎普通标签开始标记
    'TMPL_R_DELIM' => '}>',        // 模板引擎普通标签结束标记

    /* 文件上传全局配置 */
    'FILE_UPLOAD_CONFIG' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => array('jpg', 'gif', 'png', 'jpeg', 'zip', 'rar', 'tar', 'gz', '7z', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'xml', 'swf', 'avi'), //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y/m/d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => UPLOAD_PATH, //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => false, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),
    'ERROR_PAGE' => '/Public/error.html',
    /* 模板解析设置 */
    'TMPL_PARSE_STRING' => array(
        './Public/upload' => SCRIPT_DIR . '/Public/upload',
        '__PUBLIC__' => SCRIPT_DIR . '/Public',
        '__STATIC__' => SCRIPT_DIR . '/Public/static',
        '__PAGE__' => SCRIPT_DIR . '/Public/view',
        '__VIEW__' => SCRIPT_DIR . '/App/Admin/View',
        '__JS__' => SCRIPT_DIR . '/App/Admin/View',
    ),
    'API_AUTH_APISECRET' => 'qrVCeAtkc4keji',
    'API_CHECK_APISECRET'=> 'c8428d0773fc7a656b7cfcfb0cb4ac62',
);
