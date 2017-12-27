<?php
namespace Caryu\Controller;

class ConfigController extends CommonController {

    protected  $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = M('config');
    }

    /**
     * 首页
     */
    public function index(){
        $this->display();
    }


    /**
     * 列表
     */
    public function getConfigList(){
       $data['list'] =  $this->config->select();
       $data['total'] =  $this->config->count();
       ajax_output(0,'success',$data);
    }

    /**
     * 获取配置项目
     */
    public function getConfig(){
        $param = array('key');
        $check = array();
        $data = $this->checkParam($param, $check);

        $res = $this->config->where(['key'=>$data['key']])->find();
        ajax_output(0,'success',$res);
    }

    /**
     * 设置配置项
     */
    public function updateConfig(){
        $param = array('key','value','explain');
        $check = array();
        $data = $this->checkParam($param, $check);
        $data['update_time'] = SYS_DATE;
        $res = $this->config->where(['key'=>$data['key']])->save($data);
        echo $this->config->_sql();
        ajax_output(0,'success');
    }

    /**
     * 添加配置项
     */
    public function addConfig(){
        $param = array('key','value','explain');
        $check = array();
        $data = $this->checkParam($param, $check);
        $data['update_time'] = SYS_DATE;
        $res = $this->config->add($data);
        if($res){
            ajax_output(0,'success');
        }else{
            ajax_output(1,'请确认KEY值唯一');
        }
    }





}
