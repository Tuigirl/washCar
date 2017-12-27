<?php
namespace Wechat\Controller;

class TimeTaskController extends CommonController
{
    /**
     * 违章订单过期(15分钟)
     */

    public function carViolationOverdue()
    {
        $model =  M('car_violation_order');
        $where['orderStatus'] = 0;
        $orderList =  $model->where($where)->select();

        foreach ($orderList as $key => $value){

            $OverDueTime = strtotime($value['add_time']) + 15 * 60; //15分钟过期
            $res = $OverDueTime > SYS_TIME ? 0: 1;
            if($res){
                $model->where(['violation_order_id'=>$value['violation_order_id']])->save(['orderStatus'=>'-1']);
            }
        }
    }
}