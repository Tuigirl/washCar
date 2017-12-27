<?php
namespace Caryu\Controller;

class WashCarController extends CommonController
{

    protected $washCar;

    public function __construct()
    {
        parent::__construct();
        $this->washCar = D('WashCar');
    }


    public function index()
    {
        $this->display();
    }

    public function feedBackIndex()
    {
        $this->display();
    }


    /**
     * 洗车订单列表
     */

    public function orderList()
    {

        $device = I('device', '', 'trim');
        $timeStar = I('time_star', '', 'trim');
        $timeEnd = I('time_end', '', 'trim');
        $toExcel = I('to_excel', '', 'trim');
        $orders = I('orders', '', 'trim');
        $limit = I('limit', '20', 'trim');
        $page = I('page', '1', 'trim');

        if ($device) $where['device'] = $device;
        if ($timeStar && $timeEnd){
            $where['pay_time'] = array('between', [$timeStar . " 00:00:00", $timeEnd . '23:59:59']);
        }else{
            $where['pay_time'] = ['neq', '0000-00-00 00:00:00'];
        }

        $data['total'] = $this->washCar->where($where)->count();

        if ($toExcel) {
            if ($orders) $where['order_id'] = array('in', $orders);
            $data['list'] = $this->washCar->where($where)->order('pay_time DESC')->field('order_id,device,pay_money,pay_time')->select();
            $data['list'] = empty($data['list']) ? [] : $data['list'];
            $Excel = $this->washCar->WashCarExcel($data);
            ajax_output(0, 'success', $Excel);
        } else {
            $data['list'] = $this->washCar->where($where)->order('pay_time DESC')->limit($limit)->page($page)->field('order_id,device,pay_money,pay_time')->select();
            $data['list'] = empty($data['list']) ? [] : $data['list'];
            ajax_output(0, 'success', $data);
        }
    }


    /**
     * 问题反馈
     */

    public function feedBack()
    {
        $city = I('city', '', 'trim');
        $device = I('device', '', 'trim');
        $timeStar = I('time_star', '', 'trim');
        $timeEnd = I('time_end', '', 'trim');
        $toExcel = I('to_excel', '', 'trim');

        if ($city) $where['city'] = $city;
        if ($timeStar && $timeEnd) $where['add_time'] = array('between', [$timeStar . " 00:00:00", $timeEnd . ' 23:59:59']);
        if ($device) $where['device'] = $device;
        $where['feedBack'] = ['neq', ''];

        $data['list'] = $this->washCar->where($where)->order('add_time DESC')->field('device,city,add_time,feedBack,city_name')->select();
        $data['total'] = $this->washCar->where($where)->count();

        if ($toExcel) {
            $excel = $this->washCar->feedBackExcel($data);
            ajax_output(0, 'success', $excel);
        } else {
            $data['list'] = empty($data['list']) ? [] : $data['list'];
            ajax_output(0, 'success', $data);
        }
    }

}