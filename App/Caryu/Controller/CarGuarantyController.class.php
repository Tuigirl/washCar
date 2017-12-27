<?php
namespace Caryu\Controller;
require_once THINK_PATH . 'Library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class CarGuarantyController extends CommonController
{
    //APP除唐山的订单列表
    private $StatusList = [
        ['id' => 0, 'name' => '全部订单'],
        ['id' => 1, 'name' => '已提交'],
        ['id' => 2, 'name' => '办理中'],
        ['id' => 3, 'name' => '贷款成功'],
        ['id' => 4, 'name' => '贷款失败']
    ];
    private $status_tsList = [
        ['id' => 0, 'name' => '全部订单'],
        ['id' => 1, 'name' => '未查看'],
        ['id' => 2, 'name' => '已查看'],
        ['id' => 3, 'name' => '已沟通'],
        ['id' => 4, 'name' => '审核中'],
        ['id' => 5, 'name' => '签约中'],
        ['id' => 6, 'name' => '合规中'],
        ['id' => 7, 'name' => '放款中'],
        ['id' => 8, 'name' => '贷款成功'],
        ['id' => 9, 'name' => '贷款失败']
    ];

    public function index()
    {
        //获取改用户可查询的所有城市
        $user_city = $this->getUserCity();
        $status_tsList = $this->status_tsList;
        $status_sdList = $this->StatusList;
        $this->assign('city_list', $user_city);
        $this->assign('status_jdList', $status_tsList);
        $this->assign('status_sdList', $status_sdList);
        $this->assign('order_status', A('JiuDing')->order_status);
        $this->display();
    }

    /**
     * 获取列表
     */
    public function getlist()
    {
        $list = D('CarGuaranty')->getlist($_REQUEST);
        echo json_encode($list);
    }

    /**
     * 编辑单个车抵贷订单状态
     * 请求参数：
     */
    public function edit()
    {
        $param = array('id', 'status');
        $check = array();
        $data = $this->checkParam($param, $check);
        $user_id = cookie('saas_admin_userid');

        if(empty($user_id)){
            ajax_output(2,'登录过期，请先登录！');
        }
        $model = D('CarGuaranty');
        $city_id = $model->getCityId($data['id']);

        if($city_id == 128){
            $check_res = $model->CheckStatus($data['id'],$data['status']);
        }else{
            $check_res = $model->CheckStatus_sd($data['id'],$data['status']);
        }

        //错误的操作直接拒绝
        if($check_res['code']){
            ajax_output($check_res['code'],$check_res['msg']);
        }
        $flag = false;

        //九鼎的交易成功...
        if(($data['status'] == 10 && $city_id == 128) || $data['status'] == 3 && $city_id != 128){
            $shop_login_id = M('CarGuaranty')->where('id = '.$data['id'])->getField('shop_login_id');

            if(!empty($shop_login_id)){
                if($_REQUEST['shop_income']){
                    $order_res = D('CarGuaranty')->shopBenefit($data['id'],$data['status'],$data['shop_income'],$user_id);
                    $flag = true;
                }else{
                    ajax_output('1','未填写商家收益!');
                }
            }else{
                $where['id'] = $data['id'];
                $save['status'] = $data['status'];
                M('CarGuaranty')->where($where)->save($save);
                $data['remark'] = $model->getRemark($data['status']);
                D('CarGuaranty')->handleLog($user_id,$data['id'],$check_res['old_status'],$save['status'],$data['remark']);
                ajax_output('0','状态修改成功!');
                die();
            }
        }

        //车闪贷的订单状态
        if($data['status'] != 3 && $city_id !=128){
            $where['id'] = $data['id'];
            $save['status'] = $data['status'];
            M('CarGuaranty')->where($where)->save($save);
            $data['remark'] = $model->getRemark($data['status']);
            D('CarGuaranty')->handleLog($user_id,$data['id'],$check_res['old_status'],$save['status'],$data['remark']);
            ajax_output('0','状态修改成功！');
        }



        if($flag) ajax_output('0','填写商家收益成功！');
        else ajax_output('1','填写商家收益失败！');
    }

    /**
     * 打印excel
        $_REQUEST['time_star'] = '2017-6-5';
        $_REQUEST['time_end'] = '2017-6-7';
     */
    public function Excel()
    {
        $param = array('time_star', 'time_end');
        $check = array();
        $data = $this->checkParam($param, $check);

        $list = D('CarGuaranty')->getlist($_REQUEST);
//        var_dump($list['rows']);die;
        $path = D('CarGuaranty')->Excel($list['rows']);
        $path = WEB_PATH . ltrim($path, '.');  //导出excel表格， 并返回下载地址
        
        ajax_output(0, '', $path);
    }

    //订单的变更历史
    public function history(){
        $param = array('id');
        $check = array();
        $data = $this->checkParam($param, $check);

        $city_id = D('CarGuaranty')->getCityId($data['id']);
        if($city_id == 128){
            A('JiuDing')->history($data['id']);
        }else{
            $this->history_sd($data['id']);
        }
    }

    private function history_sd($id){

        $list['rows'] = D('car_guaranty_log')->join('admin as a on a.id = car_guaranty_log.admin_id')
            ->where(array('car_guaranty_log.order_id'=>$id))->Field('car_guaranty_log.*,a.user_name')->select();
        $list['info'] = D('car_guaranty')->where('id = '.$id)->Field('add_time,id')->find();
        foreach ($list['info'] as $key => $value){
            $list['info'][$key]['status_name'] = R('Api/CarGuaranty/getStatusInfo',array($value['to'],2)); //非唐山即可
        }
        echo json_encode($list);
    }
}