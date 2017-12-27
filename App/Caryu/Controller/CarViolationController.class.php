<?php
namespace Caryu\Controller;

/**
 * 车违章
 */

class CarViolationController extends CommonController {

    public function index()
    {
        $orderStatus = array(
            4 => '全部',
            0 => '已提交',
            1 => '办理中',
            2 => '已完成',
            3 => '已退款',
            -1 => '已失效'
        );

        $this->assign('city_list', $this->getUserCity());
        $this->assign('order_status', $orderStatus);
        $this->display();
    }

    /**
     * 车违章列表
     */
    public function getList(){
        $list = $this->getOrderlist($_REQUEST);
        echo json_encode($list);
    }

    public function getOrderlist($data, $limit = 1)
    {
        //违章待缴的订单没有城市属性
        $row = $data['rows'] ? $data['rows'] : 20;
        $page = $data['page'] ? $data['page'] : 1;

        if ($data['time_star'] && empty($data['time_end'])) $where['add_time'] = array('gt', $data['time_star']);
        if ($data['time_end'] && empty($data['time_star'])) $where['add_time'] = array('lt', $data['time_star'] . " 23:59:59");
        if ($data['time_end'] && $data['time_star']) $where['add_time'] = array('between', array($data['time_star'], $data['time_end'] . " 23:59:59"));
        if ($data['orderStatus'] == '0' || ($data['orderStatus'] != '4' && $data['orderStatus'])) $where['orderStatus'] = $data['orderStatus'];
        if ($data['license_number']) $where['license_number'] = $data['license_number'];
//        if ($data['order_id']) $where['violation_order_id|third_order_id'] =array($data['order_id'],$data['order_id'],'_multi'=>true);
        if ($data['orderid']) $where['violation_order_id'] = $data['orderid'];
        if ($data['source']) $where['source'] = $data['source'];

        if ($limit) {
            $list  = M('car_violation_order')->where($where)->limit($row)->page($page)->order('add_time DESC')->select();
            $count = M('car_violation_order')->where($where)->count();
        } else {
            $list = M('car_violation_order')->where($where)->order('add_time DESC')->select();  //导出excel
        }

        return $arr = [
            'rows' => $list ? $list : [],
            'total' => $count ? $count :0
        ];
    }
}