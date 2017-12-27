<?php
namespace Xiaochengxu\Controller;

class WashCarController extends CommonController
{
    private $rocord;
    private $caryu_config;
    private $washcar;
    private $testNO = 170005;
    private $testOpenid = 'oQZQa0e2gk79uD-_jjlDnz1TE0pQ';
    private $redPacket;
    private $wxPay;

    //友洗
    private $youxi = [170005];


    public function __construct()
    {
        $this->rocord = D('WashRecord');
        $this->caryu_config = D('Caryu/Config');
        $this->redPacket = D('RedPacket');
        $this->washcar = D('YouXi');
        $this->wxPay = A('WxPay');
    }


    /**
     * 扫描二维码发送洗车请求
     */
    public function toWash()
    {
        $_REQUEST['DeviceSerial'] = $this->testNO;
        $_REQUEST['openid'] = $this->testOpenid;

        $param = array('DeviceSerial', 'openid');
        $check = array();
        $data = $this->checkParam($param, $check, 1);

        //检测设备的渠道
        if (in_array($data['DeviceSerial'], $this->youxi)) {
            //检测洗车是否可工作
            $res = $this->washcar->checkDevice($data['DeviceSerial']);
            if ($res['status']) {
                ajax_output(1, $res['message']);
            }
        }

        //获取设置本次订单金额
        $config = $this->caryu_config->getValue('clear_car');
        if (empty($config)) {
            ajax_output(1, '未获取到配置项');
        }

        //检测优惠券
        $redPacket = $this->redPacket->washCarRedPacket($data['openid']);

        $data['cost_money'] = $config['cost_money'];    //订单金额
        $data['cost_time'] = $config['cost_time'];      //洗车时间
        $data['redPacket'] = $redPacket;                //红包

        ajax_output(0, 'success', $data);
    }


    /**
     *  支付
     */

    public function toPay()
    {
        $param = array('openid', 'DeviceSerial');
        $check = array();
        $data = $this->checkParam($param, $check, 1);
        $redPacketId = $_REQUEST['redPacketId'];

        //检测设备的渠道
        if (in_array($data['DeviceSerial'], $this->youxi)) {
            //检测洗车是否可工作
            $res = $this->washcar->checkDevice($data['DeviceSerial']);
            if ($res['status']) {
                ajax_output(1, $res['message']);
            }
        } else {
            ajax_output(1, '该设备不存在！');
        }

        //配置中的付款金额
        $config = $this->caryu_config->getValue('clear_car');
        if (empty($config['cost_money'])) {
            ajax_output(1, '未获取到配置项');
        }

        //红包金额
        $redPacket = $this->redPacket->getRedPacket($data['openid'], $redPacketId);

        if ($redPacket['can_use']) {
            $data['pay_money'] = $config['cost_money'] - $redPacket['money'];
        } else {
            $data['pay_money'] = $config['cost_money'];
        }

        //生成订单
        $res = $this->rocord->addWashCarOrder($data['pay_money'], $data['openid'], $data['DeviceSerial'], $redPacket, $config['cost_money']);
        if ($res['code']) {
            ajax_output(1, '请重新支付');
        } else {
            $this->wxPay->wxPay($res['data']['order_id'], 'washCar');
        }
    }


    /**
     * 支付成功
     */

    public function paySuccess()
    {
        $orderid = I('orderid', '2017120117000527368', 'trim');
        if (empty($orderid)) die;
        $this->rocord->paySuccess($orderid);
    }


    /**
     * 检测订单状态
     */

    public function checkOrderStatus()
    {
        $param = array('orderid');
        $check = array();
        $data = $this->checkParam($param, $check, 1);

        $status = $this->rocord->where(['order_id' => $data['orderid']])->getField('status');
        ajax_output(0, 'success', $status);
    }


    /**
     * 问题反馈(订单)
     */

    public function orderFeedBack()
    {
        $param = array('openid', 'orderid', 'desc');
        $check = array();
        $data = $this->checkParam($param, $check, 1);

        $orderInfo = $this->rocord->where(['openid' => $data['openid'], ['order_id' => $data['orderid']]])->find();
        if ($orderInfo) {
            $res = $this->rocord->where(['openid' => $data['openid'], ['order_id' => $data['orderid']]])
                ->save(['feedBack' => $data['desc'], 'update_time' => SYS_DATE]);
            ajax_output(0, 'success', $res);
        } else {
            ajax_output(1, '未查到该用户下的订单! 订单号：' . $data['orderid']);
        }
    }

    /**
     * 获取洗车的状态和剩余的时间
     */

    public function getOrderStatus()
    {
        $param = array('openid');
        $check = array();
        $data = $this->checkParam($param, $check, 1);

        $config = $this->caryu_config->getValue('clear_car');

        //查询付款之后20分钟内的订单
        $where['openid'] = $data['openid'];
        $where['pay_time'] = ['gt', date('Y-m-d H:i:s', time() - $config['cost_time'] * 60)];
        $orderInfo = $this->rocord->where($where)->find();

        if ($orderInfo) {
            $device = $this->washcar->checkDevice($orderInfo['device']);
            $orderInfo['washing'] = $device['status'];  //0:没有洗车 1正在洗车
            $used_time = SYS_TIME - strtotime($orderInfo['pay_time']);
            $orderInfo['time_left'] = $config['cost_time'] - $used_time;
            ajax_output(0, 'success', $orderInfo);
        } else {
            $orderInfo['washing'] = 0;
            ajax_output(0, '未查找到活跃的订单', $orderInfo);
        }
    }
}
