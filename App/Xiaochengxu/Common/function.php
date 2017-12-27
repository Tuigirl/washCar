<?php
/**
 * 获取数据字典
 * @param $key      //键值，方便查找数据
 * @param $fileName //字典文件名 目录Common/Dict/
 * @return mixed
 */
function dict($key = '', $fileName = 'Setting') {
    static $_dictFileCache  =   array();
    $file = MODULE_PATH . 'Common' . DS . 'Dict' . DS . $fileName . '.php';
    if (!file_exists($file)){
        unset($_dictFileCache);
        return null;
    }
    if(!$key && !empty($_dictFileCache)) return $_dictFileCache;
    if ($key && isset($_dictFileCache[$key])) return $_dictFileCache[$key];
    $data = require_once $file;
    $_dictFileCache = $data;
    return $key ? $data[$key] : $data;
}
function meauCheckPower($controller,$action) {
    $auth = new \Think\Auth();
        /*var_dump($auth->getGroups('2'));
        var_dump($_config);*///MODULE_NAME
        $str = $controller.'/'.$action.','.$controller.'/all'.',all/all';
        /*echo $auth->getGroups(session('saas_admin_userid'));
        var_dump($str);
        echo session('saas_admin_userid');*/
        $authFlag =  $auth->check( $str, session('saas_admin_userid'));
        if(!$authFlag){
            return false;
        }else return true;
}
/**
 * tp 的save 成功失败验证
 * @param $res save操作的结果
 * @return bool
 */
function  checkSaveResult($res){
    if($res !== false) return ture;
    else return false;
}

