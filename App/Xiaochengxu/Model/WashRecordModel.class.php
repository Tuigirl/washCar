<?php
namespace Xiaochengxu\Model;

use Think\Model;

class WashRecordModel extends Model{

    protected $tableName = 'wash_order';
    protected $pk = 'id';
    protected $status = [
        1 => '已申请',
        2=> '交易成功',
        '-1'=> '交易失败',
        '-2'=> '失效',
    ];


    /**
     *  生成洗车订单
     * @param $pay_money        支付金额
     * @param $openid
     * @param $DeviceSerial     设备序列号
     * @param $redPacket        红包参数
     * @param $set_money        系统设置的配置金额
     */

    public function addWashCarOrder($pay_money,$openid,$DeviceSerial,$redPacket,$set_money)
    {
        $add['order_id'] = getOrderId($DeviceSerial);
        $add['device'] = $DeviceSerial;
        $cityInfo = deviceCityInfo($DeviceSerial);
        $add['city'] = $cityInfo['id'];
        $add['city_name'] = $cityInfo['name'];
        $add['pay_money'] = $pay_money;
        $add['set_money'] = $set_money;
        $add['openid'] = $openid;
        $add['red_packet_id'] = empty($redPacket['id']) ? '': $redPacket['id'];
        $add['red_packet_money'] = empty($redPacket['money']) ? '': $redPacket['money'];
        $add['add_time'] = SYS_DATE;
        $add['update_time'] = SYS_DATE;
        $add['pay_time'] = '';
        $add['status'] = 1;
        $res = $this->add($add);

        if($res){
            return return_output(0,'success',$add);
        }else{
            return return_output(1,$this->_sql());
        }
    }

    /**
     * 交易成功
     */

    public function paySuccess($orderId){

        $where['order_id'] = $orderId;
        $orderInfo = M('wash_order')->where($where)->find();

        if($orderInfo){

            //更改订单状态
            $save['status'] = 2;
            $save['update_time'] = SYS_DATE;
            $save['pay_time'] = SYS_DATE;
            $data['order']['res'] =  $this->where($where)->save($save);
            $data['order']['sql'] = $this->_sql();

            //更改优惠券的状态
            if($orderInfo['red_packet_id']){
               $data['redPacket']['res'] =  M('red_packet_rocord')->where(['id'=>$orderInfo['red_packet_id']])->save(['status'=>1,'use_time'=>SYS_DATE]);
               $data['redPacket']['sql'] =  M('red_packet_rocord')->_sql();
            }else{
                $data['redPacket']['res'] = '没有优惠券';
                $data['redPacket']['sql'] = '';
            }

            //启动设备
            $data['device']= D('YouXi')->deviceSerialStar($orderInfo['device']);

            //发送模板消息
            A('WeChat')->sendWashCarSuccess($orderInfo);

            //保存执行结果
            $this->where($where)->save(['json_data'=>json_encode($data)]);
        }
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