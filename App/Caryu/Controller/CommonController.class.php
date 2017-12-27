<?php
namespace Caryu\Controller;

use Think\Controller;
define('G_HTTP_HOST', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
define('G_HTTP', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
define("WEB_PATH", dirname(G_HTTP . G_HTTP_HOST . $_SERVER['SCRIPT_NAME']));
class CommonController extends Controller{


    public function __construct()
    {
        parent::__construct();
        if(IS_AJAX && IS_GET) C('DEFAULT_AJAX_RETURN', 'html');
        self::authCheck();
        self::check_admin();
        self::check_priv();
        self::manage_log();
        //记录上次每页显示数
        if(I('get.grid') && I('post.rows')) cookie('pagesize', I('post.rows', C('DATAGRID_PAGE_SIZE'), 'intVal'));
    }

    /**
     *  参数检测与过滤
     */
    public function checkParam($param,$check = array(),$nosign=0){

        //校验sign
        $data = $_REQUEST;
        //过滤不合法
        $data = array_map(function ($v){
            return htmlspecialchars($v);
        }, $data);
        foreach ($param as $k =>$r){
            foreach ($data as $key =>$row){
                $isset = 0;
                if($key == $r ){
                    $isset = 1;
                    if(!empty($check[$k])){
                        $func = $check[$k];
                        $data[$r] = $func($row);
                    }
                    //如果参数不存在 或者参数为空 且不等于0 (string，int)
                    if(!isset($data[$r]) || (empty($data[$r]) &&  !($data[$r] === '0' || $data[$r] === 0) )){
                        ajax_output('1',"缺少参数 code 1:".$r);
                    }
                    break;
                }
            }
            if($isset == 0){
                ajax_output('1',"缺少参数 code 2:".$r);
            }
        }
        return $data;
    }



    //权限验证
    private function  authCheck(){
        //无需校验的控制器和方法
        $no_check = C('NO_CHECK_CONFIG');
        $auth = new \Think\Auth();
        $str = CONTROLLER_NAME.'/'.ACTION_NAME.','.CONTROLLER_NAME.'/all'.',all/all';
        if(CONTROLLER_NAME == 'Index') return true;
        foreach ($no_check as $key => $value){
            if(CONTROLLER_NAME == $value['CONTROLLER_NAME'] && ACTION_NAME == $value['ACTION_NAME']){
                return true;
            }
        }

        $authFlag =  $auth->check( $str, session('saas_admin_userid'));
        /*var_dump($authFlag );exit;*/
        if(!$authFlag){
            if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
                $res = array('info'=>'对不起，您没有操作权限1','status'=>0);
                ajax_output("0",'对不起，您没有操作权限2');
            }else{
                echo "<script>alert('对不起，您没有操作权限3');function goRe(){ ; } setTimeout('goRe()',1000)</script>";die;
            }
        }
    }


    /**
     * 判断用户是否已经登陆
     */
    final public function check_admin() {
        if(CONTROLLER_NAME =='Index' && in_array(ACTION_NAME, array('login', 'code')) ) {
            return true;
        }
        if(!session('saas_admin_userid') || !session('saas_admin_username')){
            //针对iframe加载返回
            if(IS_GET && strpos(ACTION_NAME,'_iframe') !== false){
                exit('<style type="text/css">body{margin:0;padding:0}a{color:#08c;text-decoration:none}a:hover,a:focus{color:#005580;text-decoration:underline}a:focus,a:hover,a:active{outline:0}</style><div style="padding:6px;font-size:12px">请先<a target="_parent" href="'.U('Index/login').'">登录</a>后台管理</div>');
            }
            if(IS_AJAX && IS_GET){
                exit('<div style="padding:6px">请先<a href="'.U('Index/login').'">登录</a>后台管理</div>');
            }else {
                $this->redirect(U('Index/login'));
            }
        }
    }

    /**
     * 权限判断
     * 未启用
     */
    final public function check_priv() {
        return true;
        if(session('roleid') == 1) return true;
        //过滤不需要权限控制的页面
        switch (CONTROLLER_NAME){
            case 'Index':
                switch (ACTION_NAME){
                    case 'index':
                    case 'login':
                    case 'code':
                    case 'logout':
                        return true;
                        break;
                }
                break;
            case 'Upload':
                return true;
                break;
            case 'Content':
                if (ACTION_NAME != 'index') return true;
                break;
        }
        if(strpos(ACTION_NAME,'public_')!==false) return true;
    }


    /**
     * 记录日志
     * 未启用
     */
    final private function manage_log(){
        //判断是否记录
        if(C('SAVE_LOG_OPEN')){
            $action = ACTION_NAME;
            if($action == '' || strchr($action,'public') || (CONTROLLER_NAME =='Index' && in_array($action, array('login','code'))) ||  CONTROLLER_NAME =='Upload') {
                return false;
            }else {
                $ip = get_client_ip();
                $username = cookie('username');
                $userid = session('userid');
                $time = date('Y-m-d H-i-s');
                $data = array('GET'=>$_GET);
                if(IS_POST) $data['POST'] = $_POST;
                $data_json = json_encode($data);
                $log_db = M('log');
                $log_db->add(array('username'=>$username,'userid'=>$userid,'controller'=>CONTROLLER_NAME,'action'=>ACTION_NAME, 'querystring'=>$data_json,'time'=>$time,'ip'=>$ip));
            }
        }
    }


    /**
     * 获取当前用户能查看的city
     */
    public function getUserCity(){
        $user_id = cookie('saas_admin_userid');
        if($user_id){
            $where['uid'] = $user_id;
            $user_city =  M('admin_city')->where($where)->field('city_id,city_name')->find();

            $city_id = explode(',',$user_city['city_id']);
            $city_name = explode(',',$user_city['city_name']);

            foreach ($city_id as $key => $value){
                $arr[] = [
                    'city_id' => $value,
                    'city_name'=> $city_name[$key]
                ];
            }

            if(count($arr)>1){
                $all = ['city_id'=>0,'city_name'=>'全部'];
                array_unshift($arr,$all);
            }
            return $arr;
        }else{
            return [];
        }
    }

    /**
     * 获取当前用户能查看的city
     */
    public function getUserCitys(){
        $user_id = cookie('saas_admin_userid');
        if($user_id){
            $where['uid'] = $user_id;
            $user_city =  M('admin_city')->where($where)->field('city_id,city_name')->find();

            $city_id = explode(',',$user_city['city_id']);
            $city_name = explode(',',$user_city['city_name']);

            foreach ($city_id as $key => $value){
                $arr[] = [
                    'id' => $value,
                    'name'=> $city_name[$key]
                ];
            }
            return $arr;
        }else{
            return [];
        }
    }


    public function checkRequestCity(){
        return 1326;
    }

    public function getAdminCity(){
        $arr = [
            ['id'=>0, 'name'=>'全部'],
            ['id'=>1326, 'name'=>'厦门'],
        ];
        echo json_encode($arr);
    }

    public function getSource(){
        $arr = [
            ['label'=>'全部', 'value'=>'all'],
            ['label'=>'微信公众号', 'value'=>'wechat'],
            ['label'=>'洗车机', 'value'=>'print_file'],
        ];
        echo json_encode($arr);
    }

}