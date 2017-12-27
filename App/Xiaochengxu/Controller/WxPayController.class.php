<?php
namespace Xiaochengxu\Controller;

use Think\Controller;

class WxPayController extends Controller
{

    public function wxPay($orderId, $type)
    {
        $res = $this->toWxPay($orderId, $type);
        ajax_output(0, 'success', $res);
    }

    //微信支付
    public function toWxPay($orderId, $db)
    {
        Vendor('Wxpay.WechatAppPay#class');
        //统一下单方法wechatAppPay
        $wechatAppPay = new  \wechatAppPay(C('weixin_config'));
        $params['body'] = $this->getObject($db);                //商品描述
        $params['out_trade_no'] = $orderId . rand(111, 999);    //自定义的订单号
        $params['attach'] = $db;                                //回调地址传参
        $data = $this->getPrice($db, $orderId);
        $total_fee = $data['pay_money'];
        $params['total_fee'] = $total_fee * 100;                //订单金额 只能为整数 单位为分*/

        $params['trade_type'] = 'JSAPI';                        //交易类型 JSAPI | NATIVE | APP | WAP
        $params['openid'] = $data['openid'];                        //交易类型 JSAPI | NATIVE | APP | WAP
        $result = $wechatAppPay->unifiedOrder($params);

        //创建小程序预支付参数
        $data = @$wechatAppPay->getAppPayParams($result['prepay_id']);
        $this->toSavePrepayId($orderId, $data['prepayid'], $db);
        $arr = array(
            'payCode' => 'Wechat',
            'data' => array(
                'timeStamp' => $data['timestamp'],
                'nonceStr' => $data['noncestr'],
                'package' => 'prepay_id=' . $data['prepayid'],
                'signType' => 'MD5',
                'paySign' => @$wechatAppPay->getPaySign($data),
                'complete' => C('weixin_config')['notify_url'],
                'orderId' => $orderId
            )
        );
        return $arr;
    }

    /**
     * 保存prepay_id
     */

    public function toSavePrepayId($order_id, $prepay_id, $db)
    {
        $save['update_time'] = SYS_DATE;
        $save['prepay_id'] = $prepay_id;
        switch ($db) {
            case 'washCar':
                M('wash_order')->where(['order_id' => $order_id])->save($save);
                break;
            case 'carViolation':
                M('car_violation_order')->where(['violation_order_id' => $order_id])->save($save);
                break;
        }
    }


    /**
     *  获取设备描述
     */

    public function getObject($db)
    {
        switch ($db) {
            case 'washCar':
                $Object = '驾遇快洗';
                break;
            case 'carViolation':
                $Object = '驾遇违章';
                break;
            default:
                $Object = false;
        }
        return $Object;
    }

    //获取订单信息
    public function getPrice($db, $order_id)
    {

        switch ($db) {
            case 'washCar':
                $info = M('wash_order')->where(['order_id' => $order_id])->find();
                break;
            case 'carViolation':
                $info = M('car_violation_order')->where(['violation_order_id' => $order_id])->field('openid,amount as pay_money')->find();
                break;
            default:
                $info = [];
        }
        return $info;
    }


    //微信支付回调函数
    public function mNotifyurl()
    {
        Vendor('Wxpay.WechatAppPay#class');
        $wechatAppPay = new \wechatAppPay();

        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $data = array();
        if (empty($xml)) {
            return false;
        }
        $data = $wechatAppPay->xml_to_data($xml);

        if (!empty($data['return_code'])) {
            if ($data['return_code'] == 'FAIL') {
                return false;
            } else {
                $orderId = substr($data['out_trade_no'], 0, -3);

                if ($data['attach'] == 'washCar') {
                    D('WashRecord')->paySuccess($orderId);
                }

                if($data['attach'] == 'carViolation'){
                    A('CarViolation')->orderPaySuccess($orderId);
                }

                echo 'success';
            }
        }
    }



    public function pay($order_sn, $money, $openId, $Goods_tag, $callBackUrl)
    {
        if (empty($order_sn) || empty($money) || empty($openId)) {
            ajax_output('1', '非法访问');
        }
        Vendor('WxpayAPI.example.JsApiPay');
        $tools = new \JsApiPay();

        $input = new \WxPayUnifiedOrder();
        $input->SetBody("驾遇汽车平台");
        $input->SetAttach("驾遇汽车平台");
        $input->SetOut_trade_no($order_sn);
        $input->SetTotal_fee($money * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($Goods_tag);
        $input->SetNotify_url($callBackUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $editAddress = $tools->GetEditAddressParameters();
        $result['jsApiParameters'] = $jsApiParameters;
        $result['editAddress'] = $editAddress;
        echo json_encode($result);
    }

    public function CarInsuranceNotify()
    {
        Vendor('WxpayAPI.example.notify');
        $notify = new \PayNotifyCallBack();
        $notify->source = 'carInsurance'; // 车违章
        $notify->Handle(false);
    }
}