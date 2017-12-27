<?php
namespace Caryu\Controller;
require_once THINK_PATH . 'Library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class SecondHandController extends CommonController
{

    public $is_limit = 1;

    /**
     * 首页
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 二手车订单列表
     */
    public function secondHandList()
    {
        $city = I('city', '', 'trim');
        $orderid = I('orderid', '', 'trim');
        $status = I('status', '', 'trim');
        $mobile = I('mobile', '', 'trim');
        $timeStart = I('time_star', '', 'trim');
        $timeEnd = I('time_end', '', 'trim');
        $rows = I('rows', '', 'trim');
        $page = I('page', '', 'trim');

        if ($city) $where['city'] = $city;
        if ($orderid) $where['order_id'] = $orderid;
        if ($status) $where['status'] = $status;
        if ($mobile) $where['mobile'] = $mobile;
        if ($timeStart && empty($timeEnd)) $where['add_time'] = array('gt', $timeStart);
        if ($timeEnd && empty($timeStart)) $where['add_time'] = array('lt', $timeEnd . " 23:59:59");
        if ($timeEnd && $timeStart) $where['add_time'] = array('between', array($timeStart, $timeEnd . " 23:59:59"));

        if($this->is_limit){
            $data['list'] = M('car_secondhand')->where($where)->limit($rows)->page($page)->order('add_time DESC')->select();
        }else{
            $data['list'] = M('car_secondhand')->where($where)->select();
        }
        $data['total'] = M('car_secondhand')->where($where)->count();

        foreach ($data['list'] as $key => $value){
            $data['list'][$key]['detail_model_name'] = $this->brand_name($value['detail_model_id']);
        }

        echo json_encode($data);
    }

    /**
     * 导出列表
     */
    public function exportList()
    {
        $this->is_limit = 0;
        $data = $this->secondHandList();
        $this->export($data);
    }

    /**
     * 拼接品牌名字
     */
    public function brand_name($detail_model_id){
        $where['detail_model_id'] = $detail_model_id;
        $res = M('car_library')->where($where)->field('model_name,vehicle_level,detail_model_name')->find();
        $str = '';
        foreach ($res as $k => $v){
            $str .= $v . ' ';
        }
        $str = trim($str);
        return $str;
    }

}