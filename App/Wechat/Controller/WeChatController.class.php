<?php
namespace Wechat\Controller;

use Think\Cache\Driver\Redis;
use Wash\Controller\WechatModel;

class WeChatController extends CommonController
{
    public $AppID = 'wx8df705dff41be223';
    public $AppSecret = 'ce6b65e9284c20805f2237bcbb74ff3e';
    public $redPacket;
    public $openid;
    public $developerID;
    public $user;
    public $device;

    public function __construct()
    {
        $this->user = M('user');
        $this->redPacket = D('redPacket');
    }

    public function index()
    {
        $nonce = $_GET['nonce'];
        $token = 'washcar';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        $array = array($nonce, $timestamp, $token);
        sort($array);

        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            echo $echostr;
            exit;
        } else {
            $this->reponseMsg();
        }
    }

    /**
     * 消息回复
     */

    public function reponseMsg()
    {
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postObj = simplexml_load_string($postArr);

        $this->openid = $postObj->FromUserName;
        $this->developerID = $postObj->ToUserName;

        //换取设备的序列号
        if($postObj->EventKey){
            $EventKey = str_replace('qrscene_', '', $postObj->EventKey);
            $this->device = M('wash_device')->where(['id'=>$EventKey])->getField('washCar_device');
        }else{
            $this->device = '';
        }

        //事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            if (strtolower($postObj->Event == 'subscribe') || strtolower($postObj->Event == 'SCAN')) {
                if ($this->device) {
                    $this->sendWashCarMsg();//有设备序列号---发送洗车的通知
                } else {
                    $this->sendAboutUsMsg();//没有设备序列号--关于我们的通知
                }
            }
        }
    }

    /**
     * 用户扫码推送洗车通知
     */

    public function sendWashCarMsg()
    {
        $model = D('Wechat');
        $model->openid = $this->openid;
        $model->developerID = $this->developerID;

        $user = M('user')->where(['openId'=>(string)$model->openid])->find();

        if($user){
            $description = '自己动手洗的车才配得是爱车如命（点击查看车辆详情，让车后服务随心所欲。）';
        }else{
            $description = '用心驾驭爱车，用手驾遇快洗（现推出新用户1分洗车超值活动）';
        }

        $arr = [
            [
                'title' => '您身边的智能洗车',
                'description' => $description,
                'picUrl' => WEB_PATH . '/Public/static/image/wechat/banner.jpg',
                'url' => WEB_PATH . '/Wechat/Common/index?openid=' . $this->openid . '&DeviceSerial=' . $this->device . '#/CarWash',
            ]
        ];

        $model->sendPicMsg($arr);
        $this->addOrUpdate($this->openid);  //保存用户openid
    }

    /**
     * 扫码--没有设备信息的微信
     */

    public function sendAboutUsMsg()
    {
        $model = D('Wechat');
        $model->openid = $this->openid;
        $model->developerID = $this->developerID;

        $description = '欢迎关注驾遇快洗公众号。用心驾驭爱车，用手驾遇快洗';

        $arr = [
            [
                'title' => '驾遇快洗',
                'description' => $description,
                'picUrl' => WEB_PATH . '/Public/static/image/wechat/nodevice.jpg',
                'url' => WEB_PATH . '/Wechat/Common/index#/AboutUs',
            ]
        ];

        $model->sendPicMsg($arr);
        $this->addOrUpdate($this->openid);  //保存用户openid
    }

    /**
     * 获取accessToken
     */

    public function getAccessToken()
    {
        $redis = new Redis();
        $ACCESS_TOKEN = $redis->get('washcar_accesstoken');

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
            $redis->setex('wechat_accesstoken', $difftime - 100, $ACCESS_TOKEN);
        }
        return $ACCESS_TOKEN;
    }


    /**
     * 生成二维码
     */

    public function getwxacode($device)
    {
        $id = getDeviceId($device);
        $accessToken = $this->getAccessToken();
        //ticket
        $ticketUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;
        $ticketArr['action_name'] = 'QR_LIMIT_SCENE';
        $ticketArr['action_info'] = ['scene' => ['scene_id' => $id]];
        $ticketjson = json_encode($ticketArr, true);
        $ticket = commonCurl($ticketjson, $ticketUrl, 1);
        $ticket = json_decode($ticket, true);
        //获取图片
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket['ticket']);
        $pic = commonCurl(false, $url);
        $filepath = './Public/upload/qrcode/' . $device . '.png';
        file_put_contents($filepath, $pic);
        $merge_path = './Public/upload/Qrcode/' . $device . '.png';
        $logo_path = './Public/upload/qrcode/logo.png';
        $this->imgMerge($filepath,$logo_path,$merge_path);
        return trim($merge_path,'.');
    }

    /**
     * 图片水印
     * @param $dst_path  二维码
     * @param $src_path  logo（水印）
     * @param $merge_path  新文件地址（水印）
     */

    public function imgMerge($dst_path,$src_path,$merge_path){
        $dst = imagecreatefromstring(file_get_contents($dst_path));
        $src = imagecreatefromstring(file_get_contents($src_path));

        list($src_w, $src_h) = getimagesize($src_path);
        imagecopy($dst, $src, 156, 156, 0, 0, $src_w, $src_h);

        list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);
        switch ($dst_type) {
            case 1://GIF
                header('Content-Type: image/gif');
                imagegif($dst,$merge_path);
                break;
            case 2://JPG
                header('Content-Type: image/jpeg');
                imagejpeg($dst,$merge_path);
                break;
            case 3://PNG
                header('Content-Type: image/png');
                imagepng($dst,$merge_path);
                break;
            default:
                break;
        }
        imagedestroy($dst);
        imagedestroy($src);
    }

    /**
     * 插入用户表
     */

    public function addOrUpdate($openid)
    {
        $userInfo = $this->user->where(['openId' => (string)$openid])->find();
        $this->user->last_login_time = SYS_DATE;

        //更新用户数据
        if ($userInfo) {
            $this->user->where(['openId' => (string)$openid])->save();
        } else {
            $this->user->openId = (string)$openid;
            $this->user->add_time = SYS_DATE;
            $userInfo['NewWashCarRedPacket'] = $this->user->NewWashCarRedPacket = 0;
            $this->user->add();
        }

        //发送洗车红包
        if (!$userInfo['NewWashCarRedPacket']) $this->redPacket->washCar((string)$openid);
    }

    /**
     * 发送洗车成功通知
     */

    public function sendWashCarSuccess($orderInfo){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
        $license_number = $this->getLicenseNumber($orderInfo);
        $license_number = '京NL8026';
        $params['touser'] = $orderInfo['openid'];
        $params['template_id'] = C('washCar.paySuccess');
        $params['url'] = WEB_PATH . '/Wechat/Common/index?license_number='.$license_number.'&orderid='.$orderInfo['order_id'].'#/PrintDetails';

        if($license_number){
           $carViolation =  A('Wechat/CarViolation')->getViolation($license_number);
           $carViolation = json_decode($carViolation, true);
           $num = count($carViolation['data']['Records']);
           if($num) $remark =  '您有一条违章待处理点击在线办理';
        }

        $params['data'] = [
            'first'=>[
                'value'=> '恭喜您洗车完成，一分耕耘一分收获'
            ],
            'keyword1'=>[
                'value'=> $orderInfo['order_id']
            ],
            'keyword2'=>[
                'value'=> date("Y-m-d H:i")
            ],
            'remark'=>[
                'value'=> $remark ? $remark : '车辆电子信息单已生成，请点击详情查看'
            ],
        ];
        $params = json_encode($params);
        $res = commonCurl($params,$url,1);
    }


    public function king(){
        $orderInfo = M('wash_order')->where(['order_id'=>2017122117000567262])->find();
        $this->sendWashCarSuccess($orderInfo);
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
     *  换取公众号id
     */

    public function getWechatOpenid()
    {
        $param = array('code');
        $check = array();
        $data = $this->checkParam($param, $check,1);

        $info = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' .C('washCar.APPID') . '&secret=' .C('washCar.AppSecret') . '&code=' . $data['code'] . '&grant_type=authorization_code');
        $data = json_decode($info, true);

        if($data['errcode']){
            ajax_output(1, $data['errmsg']);
        }else{
            ajax_output(0, 'success',$data['openid']);
        }
    }

    /**
     * 重置公众号(刘志晶)
     */

    public function reset(){
        $res = M('user')->where(['openId'=>'oXJ1z0_aR0uRkXtv-Cxib5RU5Dn4'])->delete();
        dump($res);
    }

























    /**
     * 生成底部按钮
     */

    public function definedItem()
    {
        $ACCESS_TOKEN = $this->getAccessToken();
        echo $ACCESS_TOKEN;
        var_dump("<hr/>");
        //将要携带用户信息的url添加至数组
        $url = [
            //服务平台
//            'servicePlatform_CarInsurance' => WEB_PATH . "/Wechat/Common/index#/CarInsurance", //车险办理
            'servicePlatform_CarViolation' => WEB_PATH . "/Wechat/Common/index#/CarViolation",   //违章代缴
            'servicePlatform_SecondHandle' => WEB_PATH . "/Wechat/Common/index#/CarUsed",   //二手车
//            'servicePlatform_CarGuaranty' => WEB_PATH . "/Wechat/Common/index#/CarLoan",  //车抵贷

            //我的订单
            'myOrder_AboutUs' => WEB_PATH.'/Wechat/Common/index#/AboutUs',
            'myOrder_CarViolation' => WEB_PATH . "/Wechat/Common/index#/CarViolation__Orders", //车违章订单
            'myOrder_SecondHand'   => WEB_PATH . "/Wechat/Common/index#/CarUsed__Orders", //二手车订单
//            'myOrder_CarGuaranty'  => WEB_PATH . "/Wechat/Common/index#/CarLoan__Orders", //车抵贷订单
        ];

        //添加用户信息
        foreach ($url as $key => $value) {
            $base_url = urlencode($value);
            $url[$key] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . C('washCar.APPID') . '&redirect_uri=' . $base_url . '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        }

        //创建按钮
        $postArr = array(
            'button' => array(
                array(
                    'name' => '服务平台',
                    'sub_button' => array(
//                        array(
//                            'type' => 'view',
//                            'name' => '车险办理',
//                            'url' => $url['servicePlatform_CarInsurance'],
//                        ),
                        array(
                            'type' => 'view',
                            'name' => '违章代缴',
                            'url' => $url['servicePlatform_CarViolation'],
                        ),
                        array(
                            'type' => 'view',
                            'name' => '二手车买卖',
                            'url' => $url['servicePlatform_SecondHandle'],
                        ),
//                        array(
//                            'type' => 'view',
//                            'name' => '汽车贷款',
//                            'url' => $url['servicePlatform_CarGuaranty'],
//                        ),
                    )
                ),

                array(
                    'name' => '我的订单',
                    'sub_button' => array(
                        array(
                            'type' => 'view',
                            'name' => '违章订单',
                            'url' => $url['myOrder_CarViolation'],
                        ),
                        array(
                            'type' => 'view',
                            'name' => '二手车订单',
                            'url' => $url['myOrder_SecondHand'],
                        ),
//                        array(
//                            'type' => 'view',
//                            'name' => '车抵贷订单',
//                            'url' => $url['myOrder_CarGuaranty'],
//                        ),
                        array(
                            'type' => 'view',
                            'name' => '关于我们',
                            'url' => $url['myOrder_AboutUs'],
                        ),
                    )
                ),
            ),
        );
        $postJson = (json_encode($postArr));
        var_dump($postJson);
    }

}