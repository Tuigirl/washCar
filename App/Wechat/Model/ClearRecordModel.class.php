<?php
namespace Wechat\Model;

use Think\Model;

class ClearRecordModel extends Model{

    protected $tableName = 'clear_record';
    protected $pk = 'id';
    protected $status = [
        1 => '已申请',
        2=> '交易成功',
        '-1'=> '交易失败',
        '-2'=> '失效',
    ];

    /**
     * 添加记录
     */
    public function addRecord($add){
        $add['update_time'] = $add['add_time'] = SYS_DATE;
        $add['status'] = 1;
        return $this->add($add);
    }

    /**
     * 交易成功
     */
    public function paySuccess($orderId){
        $where['order_id'] = $orderId;
        $save['status'] = 3;
        $save['update_time'] = SYS_DATE;
        return $this->where($where)->save($save);
    }


    /**
     * 订单失效
     */
    public function expiry($orderId){
        $where['order_id'] = $orderId;
        $save['status'] = -1;
        $save['update_time'] = SYS_DATE;
        return $this->where($where)->save($save);
    }


}