<?php
namespace Xiaochengxu\Controller;

use Think\Cache\Driver\Redis;

class WeChatController extends CommonController
{

    public $AppID = 'wxa5a1aa8aba8eaa30';
    public $AppSecret = 'b4bc4eff5bf3c12ab58b32b3e13d7290';
    public $user;
    public $redPacket;

    public function __construct()
    {
        $this->user = M('user');
        $this->redPacket = D('redPacket');
    }


    /**
     * 获取openId
     */
    public function getOpenId()
    {
        $param = array('code');
        $check = array();
        $data = $this->checkParam($param, $check, 1);

        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $params['appid'] = $this->AppID;
        $params['secret'] = $this->AppSecret;
        $params['js_code'] = $data['code'];
        $params['grant_type'] = 'authorization_code';
        $res = commonCurl($params, $url);
        $arr = json_decode($res, true);

        if ($arr['openid']) {
            $this->addOrUpdate($arr);  //插入用户表
        }

        ajax_output(0, 'success', $res);
    }


    /**
     * 发送洗车成功的消息通知
     */

    public function sendWashCarSuccess($orderInfo)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $this->getAccessToken();

        $params['touser'] = $orderInfo['openid'];
        $params['template_id'] = C('washCar.paySuccess');
//        $params['page'] = 'pages/PrintDetails/Index?license_number='.$this->getLicenseNumber($orderInfo).'&orderid='.$orderInfo['order_id'];
        $params['page'] = 'pages/PrintDetails/Index?license_number=京N194X4&orderid='.$orderInfo['order_id'];
        $params['form_id'] = $orderInfo['prepay_id'];
        $params['data'] = $this->getData($orderInfo);

        $params = json_encode($params);
        commonCurl($params, $url, 1);
    }

    /**
     * 发送违章支付成功通知
     */

    public function sendCarViolation($orderInfo)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $this->getAccessToken();

        $tmpUrl = 'https://washcar.caryu.com/Xiaochengxu/CarViolation/paySuccess?orderid='.$orderInfo['violation_order_id'];
        $tmpUrl = urlencode($tmpUrl);
        $params['touser'] = $orderInfo['openid'];
        $params['page'] = 'pages/WebView?URL='.$tmpUrl;
        $params['template_id'] = C('washCar.carViolationPaySuccess');
        $params['form_id'] = $orderInfo['prepay_id'];
        $params['data'] = $this->getCarViolationPaySuccessData($orderInfo);

        $params = json_encode($params);
        commonCurl($params, $url, 1);
    }

    /**
     * 获取违章的data
     */

    public function getCarViolationPaySuccessData($orderInfo){
        $arr = [
            'keyword1' => [
                'value' => '违章缴费',
                'color' => '#173177',
            ],
            'keyword2' => [
                'value' => '已受理',
                'color' => '#173177',
            ],
            'keyword3' => [
                'value' => $orderInfo['amount'],
                'color' => '#173177',
            ],
            'keyword4' => [
                'value' => $orderInfo['violation_order_id'],
                'color' => '#173177',
            ],
        ];
        return $arr;
    }



    /**
     *  获取车险信息
     */

    public function getLicenseNumber($orderInfo){
        $where['device'] = $orderInfo['device'];
        $time = SYS_TIME - 15 * 60;
        $where['add_time'] =  ['gt',date('Y-m-d H:i:s',$time)];
        $license_number =  M('discern_res')->where($where)->order('add_time DESC')->getField('license_number');
        if($license_number){
            M('wash_order')->where(['order_id'=>$orderInfo['order_id']])->save(['license_number'=>$license_number]);
        }
        return $license_number;
    }

    /**
     * 拼接data
     * @param $orderInfo
     * @return array
     */

    public function getData($orderInfo)
    {

        $arr = [
            'keyword1' => [
                'value' => $orderInfo['order_id'],
                'color' => '#173177',
            ],
            'keyword2' => [
                'value' => $orderInfo['pay_money'],
                'color' => '#173177',
            ],
            'keyword3' => [
                'value' => M('wash_device')->where(['washCar_device'=>$orderInfo['device']])->getField('address'),
                'color' => '#173177',
            ],
            'keyword4' => [
                'value' => date('Y年m月d日 H:i'),
                'color' => '#173177',
            ],
            'keyword5' => [
                'value' => '查看小程序',
                'color' => '#173177',
            ]
        ];
        return $arr;
    }

    /**
     * 获取accessToken
     */

    public function getAccessToken()
    {
        $redis = new Redis();
        $ACCESS_TOKEN = $redis->get('Xiaochengxu_accesstoken');

        if (!$ACCESS_TOKEN) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token';
            $params['grant_type'] = 'client_credential';
            $params['appid'] = $this->AppID;
            $params['secret'] = $this->AppSecret;
            $res = commonCurl($params, $url);
            $arr = json_decode($res, true);

            if (!empty($arr['errcode'])) {
                die('token获取失败');
            }
            $difftime = $arr['expires_in'];
            $ACCESS_TOKEN = $arr['access_token'];
            $redis->setex('Xiaochengxu_accesstoken', $difftime - 100, $ACCESS_TOKEN);
        }
        return $ACCESS_TOKEN;
    }

    /**
     *  生成带参数的二维码
     */

    public function getwxacode($device = 170005){
        $url = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token='.$this->getAccessToken();
        $params['path'] = 'pages/CarWash/Index?DeviceSerial='.$device;
        $params['width'] = 430;
        $params = json_encode($params);
        $result = commonCurl($params, $url,1);
        $filepath = './Public/upload/qrcode/'.$device.'.png';
        file_put_contents($filepath, $result);
        return $filepath;
    }

    /**
     * 插入用户表
     */

    private function addOrUpdate($data)
    {
        $userInfo = $this->user->where(['openId' => $data['openid']])->find();
        $this->user->last_login_time = SYS_DATE;

        //更新用户数据
        if ($userInfo) {
            $this->user->where(['openId' => $data['openid']])->save();
        } else {
            $this->user->openId = $data['openid'];
            $this->user->add_time = SYS_DATE;
            $userInfo['NewWashCarRedPacket'] = $this->user->NewWashCarRedPacket = 0;
            $this->user->add();
        }

        //发送洗车红包
        if (!$userInfo['NewWashCarRedPacket']) $this->redPacket->washCar($data['openid']);
    }

}