<?php
namespace Wechat\Controller;

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
        $this->carViolation = M('car_violation_order');
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

        if ($carViolation['data']['Records']) {
            $res = json_encode($carViolation['data']['Records']);
        } else {
            $res = [];
        }

        ajax_output(0, 'success', $res);
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
        ajax_output(0, 'success', $item);
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
        ajax_output(0, 'success', $tmp);
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
        ajax_output(0, 'success', $orderInfo);
    }


    /**
     *  去支付
     */

    public function toPay()
    {
        $param = array('license_number', 'openid', 'source');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);
        $data['SecondaryUniqueCode'] = explode(',', $data['code']);

        if (empty($data['code'])) ajax_output(1, '缺少参数：code');

        if ($data['source'] == 'print_file') {
            $data['device'] = $_REQUEST['device'];
        }

        //获取违章的信息
        $carViolationInfo = $this->carViolationInfo($data['SecondaryUniqueCode'], $data['license_number']);
        if ($carViolationInfo['code']) ajax_output(1, $carViolationInfo['msg']);

        //保存订单
        $addRes = $this->saveOrder($carViolationInfo['data'], $data);
        if ($addRes['code']) ajax_output(1, $addRes['msg']);

//        //发送驾遇下单
//        $thirdOrder = $this->getThirdOrder($data['SecondaryUniqueCode'], $data['license_number'], $addRes['data']);
//        if (empty($thirdOrder['data'])){
//            M('car_violation_order')->where(['violation_order_id' => $addRes['data']])->delete();
//            ajax_output(1, $thirdOrder['msg']);
//        }
//
//        M('car_violation_order')->where(['violation_order_id' => $addRes['data']])->save(['third_order_id' => $thirdOrder['data']]);

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

    /**
     *  保存订单
     */

    public function saveOrder($carViolationInfo, $data)
    {
        $model = M('car_violation_order');
        $model->device = empty($data['device']) ? '':$data['device'];
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
        $model->source = $data['source'];
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
        //通知驾遇扣款成功
        $carinfo = A('CarInfo');
        $carinfo->is_return = 1;
        $return_msg = $carinfo->carViolationPaySuccess($order_id);
        $arr = json_decode($return_msg, true);

        //将失败的信息保存起来
        if ($arr['code']){
            $save['fail_msg'] = $return_msg;die;
        }

        $save['update_time'] = SYS_DATE;
        $save['payTime'] = SYS_DATE;
        $save['orderStatus'] = 2;
        M('car_violation_order')->where(['violation_order_id' => $order_id])->save($save);
        echo M('car_violation_order')->_sql();
    }

    /**
     * 支付成功，办理失败，退款
     */

    public function orderRefund($orderid, $fail_msg)
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
        $carVionlation = A('Wechat/CarInfo');
        $carVionlation->is_return = 1;
        $_REQUEST['license_number'] = $license_number;
        $res = $carVionlation = $carVionlation->getCarViolation();
        return $res;
    }

    /**
     * 微信用户的违章订单列表
     */

    public function wxCarViolationList()
    {
        $param = array('openid', 'rows', 'page');
        $check = array();
        $data = $this->checkParam($param, $check);

        $info['list'] = $this->carViolation->where(['openid' => $data['openid'],['orderStatus'=>['neq','-1']]])->limit($data['rows'])->page($data['page'])->order('add_time DESC')->select();
        $info['count'] = $this->carViolation->where(['openid' => $data['openid'],['orderStatus'=>['neq','-1']]])->count();

        foreach ($info['list'] as $key => &$value) {
            $max_time = strtotime($value['add_time']) + 60 * 15;
            $value['time_left'] = $max_time > SYS_TIME ? $max_time - SYS_TIME : 0;
        }

        if (empty($info['list'])) $info['list'] = [];
        ajax_output(0, 'success', $info);
    }

    /**
     * 查询违章列表
     */

    public function getCarViolation()
    {
        $param = array('openid', 'license_number', 'vin', 'engineId');
        $check = array();
        $data = $this->checkParam($param, $check);

        if(!isset($_REQUEST['unlimit'])){
            $res = $this->checkWxCache('CarViolationVisitTimeCache', 'open_id_' . $data['openid'], 3, 1);
            if (!$res) ajax_output(1, '今日查询次数已达上限');
        }

        $carVionlation = A('Wechat/CarInfo');
        $carVionlation->is_return = 1;
        $rel = $carVionlation = $carVionlation->getCarViolationList($data);
        $rel = json_decode($rel, true);
        if ($rel['code']) ajax_output(1, $rel['msg']);
        $rel = $rel['data'];

        //查找三天内的订单
        $where['license_number'] = $data['license_number'];
        $where['add_time'] = array('gt', date('Y-m-d H:i:s', strtotime('-3 day')));
        $where['add_time'] = array('lt', date('Y-m-d H:i:s', time()));
        $where['orderStatus'] = array('in', array(1, 2, 3));
        $res = M('car_violation_order')->where($where)->Field('SecondaryUniqueCodes,orderStatus')->select();

        if ($res) {
            //处理本地订单
            foreach ($res as $key => $value) {
                $TempIDs = json_decode($value['SecondaryUniqueCodes'],true);
                foreach ($TempIDs as $k => $v){
                    $arr[] = ['TempID' => $v, 'orderStatus' => $value['orderStatus']];
                }
            }

            //处理缓存数据--添加备注
            foreach ($rel as $key => $value) {
                foreach ($arr as $k => $v) {
                    if ($value['SecondaryUniqueCode'] == $v['TempID']) {
                        switch ($v['orderStatus']) {
                            case 1:
                                $rel[$key]['remark'] = '正在办理';
                                break;
                            case 2:
                                $rel[$key]['remark'] = '办理完成';
                                break;
                            case 3:
                                $rel[$key]['remark'] = '不能办理';
                                break;
                            case 0:
                                $rel[$key]['remark'] = '';
                                break;
                        }
                        $rel[$key]['remark_status'] = $v['orderStatus'];
                    }
                }
            }
        }
        ajax_output(0, 'success', $rel);
    }

    /**
     * @param $hash_name   hash名称
     * @param $cacheName   key名称
     * @param $maxTime     最大缓存值
     * @param $cacheTime   缓存时间（天）1：当天24点  2：隔天24点
     * @return bool
     */

    public function checkWxCache($hash_name, $cacheName, $maxTime, $cacheTime)
    {
        $redis = \Think\Cache::getInstance('Redis');

        if ($redis->hLen($hash_name)) {
            $curret_time = $redis->hGet($hash_name, $cacheName);
            if ($curret_time <= $maxTime - 1) {
                $redis->HINCRBY($hash_name, $cacheName, 1);
                return true;
            } else {
                return false;
            }
        } else {
            $redis->hSet($hash_name, $cacheName, 1);
            $redis->expireAt($hash_name, strtotime(date('Y-m-d', time() + ($cacheTime - 1)) . '23:59:59'));
            return true;
        }
    }
}