<?php
namespace Xiaochengxu\Controller;

class CarViolationController extends CommonController
{
    private $url;
    public $testCode = '1098373';
    public $testLic = '京N194X4';
    public $testopenid = 'oQZQa0e2gk79uD-_jjlDnz1TE0pQ';
    public $testdevice = '170005';

    public function __construct()
    {
        parent::__construct();
        $this->url = C('Caryu.url');
    }

    /**
     * 违章列表页
     */

    public function violationList()
    {
        $param = array('license_number');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);

        $carViolation = $this->getViolation($data['license_number']);
        $carViolation = json_decode($carViolation, true);

        $this->assign('item', json_encode($carViolation['data']['Records']));
        $this->display();
    }

    /**
     * 违章详情
     */

    public function violationDetail()
    {
        $param = array('code', 'license_number');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);

        $carViolation = $this->getViolation($data['license_number']);
        $carViolation = json_decode($carViolation, true);

        foreach ($carViolation['data']['Records'] as $key => $value) {
            if ($value['SecondaryUniqueCode'] == $data['code']) {
                $item = $value;
            }
        }

        if (empty($item)) $item = [];
        $this->assign('item', json_encode($item));
        $this->display();
    }

    /**
     * 确认支付页面
     */

    public function confirmOrder()
    {
        $param = array('code', 'license_number');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);
        $SecondaryUniqueCode = explode(',', $data['code']);

        $carViolation = $this->getViolation($data['license_number']);
        $carViolation = json_decode($carViolation, true);

        foreach ($carViolation['data']['Records'] as $key => $value) {
            foreach ($SecondaryUniqueCode as $k => $v) {
                if ($value['SecondaryUniqueCode'] == $v) {
                    $tmp[] = $value;
                }
            }
        }

        if (empty($tmp)) $tmp = [];
        $this->assign('item', json_encode($tmp));
        $this->display();
    }

    /**
     * 支付成功界面
     */

    public function paySuccess()
    {
        $param = array('orderid');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);

        $orderInfo = M('car_violation_order')->where(['violation_order_id' => $data['orderid']])->find();
        $this->assign('item', json_encode($orderInfo));
        $this->display();
    }


    /**
     *  去支付
     */

    public function toPay()
    {
//        $_REQUEST['license_number'] = $this->testLic;
//        $_REQUEST['openid'] = $this->testopenid;
//        $_REQUEST['device'] = $this->testdevice;
//        $_REQUEST['code'] = $this->testCode;

        $param = array('license_number', 'openid', 'device');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);
        $data['SecondaryUniqueCode'] = explode(',', $data['code']);

        //获取违章的信息
        $carViolationInfo = $this->carViolationInfo($data['SecondaryUniqueCode'], $data['license_number']);
        if ($carViolationInfo['code']) ajax_output(1, $carViolationInfo['msg']);

        //保存订单
        $addRes = $this->saveOrder($carViolationInfo['data'], $data);
        if ($addRes['code']) ajax_output(1, $addRes['msg']);

        //发送驾遇下单
        $thirdOrder = $this->getThirdOrder($data['SecondaryUniqueCode'], $data['license_number'], $addRes['data']);
        if (empty($thirdOrder['data'])) ajax_output(1, $thirdOrder['msg']);

        M('car_violation_order')->where(['violation_order_id' => $addRes['data']])->save(['third_order_id' => $thirdOrder['data']]);

        //进行支付
        if ($addRes['code']) {
            ajax_output(1, '请重新支付');
        } else {
            A('WxPay')->wxPay($addRes['data'], 'carViolation');
        }
    }

    /**
     *  发送驾遇下单
     */

    private function getThirdOrder(array $code, $license_number, $orderid)
    {
        $_REQUEST['code'] = $code;
        $_REQUEST['license_number'] = $license_number;
        $_REQUEST['orderid'] = $orderid;

        $url = $this->url . 'saveCarVionlationOrder';
        $res = commonCurl($_REQUEST, $url);
        $res = json_decode($res, true);
        return $res;
    }

//    /**
//     * 更改订单的状态
//     */
//
//    public function changeOrderStatus($order_id, $status)
//    {
//        $save['update_time'] = SYS_DATE;
//
//        //支付成功
//        if ($status == 2) {
//            $save['payTime'] = SYS_DATE;
//            $orderInfo = M('car_violation_order')->where(['violation_order_id' => $order_id])->find();
//
//            //发送模板消息
//            A('WeChat')->sendCarViolation($orderInfo);
//            //通知驾遇扣款成功
//            $carinfo = A('CarInfo');
//            $carinfo->is_return = 1;
//            $return_msg = $carinfo->carViolationPaySuccess($order_id);
//            $arr = json_decode($return_msg,true);
//            //将失败的信息保存起来
//            if($arr['code']){
//                $save['fail_msg'] = $return_msg;
//                M('car_violation_order')->where(['violation_order_id' => $order_id])->save($save);
//            }
//        }
//
//        if($status == 3){
//
//        }
//
//        $save['orderStatus'] = $status;
//        M('car_violation_order')->where(['violation_order_id' => $order_id])->save($save);
//    }

    /**
     *  保存订单
     */

    public function saveOrder($carViolationInfo, $data)
    {
        $model = M('car_violation_order');
        $model->device = $data['device'];
        $model->SecondaryUniqueCodes = json_encode($data['SecondaryUniqueCode']);
        $model->violation_order_id = $order_id = getOrderId($data['device']);
        $model->amount = $carViolationInfo['amount'];
        $model->license_number = $data['license_number'];
        $model->orderStatus = 0;
        $model->poundage = $carViolationInfo['poundage'];
        $model->num = $carViolationInfo['num'];
        $model->fine = $carViolationInfo['fine'];
        $model->degree = $carViolationInfo['degree'];
        $model->openid = $data['openid'];
        $model->add_time = SYS_DATE;
        $model->update_time = SYS_DATE;
        $res = $model->add();

        if ($res) {
            return return_output(0, 'success', $order_id);
        } else {
            return return_output(1, '保存订单失败');
        }
    }

    /**
     * 支付成功
     */

    public function orderPaySuccess($order_id)
    {
        $orderInfo = M('car_violation_order')->where(['violation_order_id' => $order_id])->find();

        //发送模板消息
        A('WeChat')->sendCarViolation($orderInfo);

        //通知驾遇扣款成功
        $carinfo = A('CarInfo');
        $carinfo->is_return = 1;
        $return_msg = $carinfo->carViolationPaySuccess($order_id);
        $arr = json_decode($return_msg, true);
        //将失败的信息保存起来
        if ($arr['code']) $save['fail_msg'] = $return_msg;

        $save['update_time'] = SYS_DATE;
        $save['payTime'] = SYS_DATE;
        $save['orderStatus'] = 2;
        M('car_violation_order')->where(['violation_order_id' => $order_id])->save($save);
    }

    /**
     * 支付成功，办理失败，退款
     */

    public function orderRefund($orderid,$fail_msg)
    {
        $save['fail_msg'] = $fail_msg;
        $save['orderStatus'] = 3;
        M('car_violation_order')->where(['violation_order_id' => $orderid])->save($save);
    }


    /**
     *  检测违章信息
     *  $SecondaryUniqueCode  违章的id
     */

    private function carViolationInfo($SecondaryUniqueCode, $license_number)
    {
        $carViolation = $this->getViolation($license_number);
        $carViolation = json_decode($carViolation, true);

        if ($carViolation['code']) return return_output(1, '未能获取的' . $license_number . '的违章信息');

        $data['num'] = count($SecondaryUniqueCode);
        $data['fine'] = 0;              //罚款金额
        $data['degree'] = 0;            //扣分
        $data['poundage'] = 0;          //服务费

        foreach ($carViolation['data']['Records'] as $key => $value) {
            foreach ($SecondaryUniqueCode as $k => $v) {
                if ($value['SecondaryUniqueCode'] == $v) {
                    $data['degree'] += $value['Degree'];
                    $data['fine'] += $value['count'];
                    $data['poundage'] += $value['Poundage'];
                    $data['info'][] = $value;
                }
            }
        }

        $data['amount'] = $data['fine'] + $data['poundage'];

        //查询到的违章和实际情况相符
        if ($data['num'] == count($data['info'])) {
            return return_output(0, 'success', $data);
        } else {
            return return_output(1, '违章订单异常');
        }
    }

    /**
     *  获取违章
     */

    public function getViolation($license_number)
    {
        $carVionlation = A('Xiaochengxu/CarInfo');
        $carVionlation->is_return = 1;
        $_REQUEST['license_number'] = $license_number;
        $res = $carVionlation = $carVionlation->getCarViolation();
        return $res;
    }

}