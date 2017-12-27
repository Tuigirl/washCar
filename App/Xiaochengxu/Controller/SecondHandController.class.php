<?php
namespace Xiaochengxu\Controller;

class SecondHandController extends CommonController
{

    /**
     * 卖车
     */
    public function sellingCar()
    {

//        $_REQUEST['mobile'] = 18141928263;
//        $_REQUEST['city'] = '北京';
//        $_REQUEST['city_id'] = 2;
//        $_REQUEST['detail_model_id'] = 268143;
//        $_REQUEST['isQuotes'] = 1;
//        $_REQUEST['openid'] = 'aaaaaaaaaaaaaaaaaaaaaaaa';
//        $_REQUEST['card_time'] = '2014-01';
//        $_REQUEST['mileage'] = '100';
//        $_REQUEST['name'] = 'king';
//        $_REQUEST['orderid'] = 11111;

        $param = array('mobile', 'city', 'city_id', 'detail_model_id', 'isQuotes','openid');
        $check = array();
        $data = $this->checkParam($param, $check, 1);
        $model = D('Xiaochengxu/SecondHand');
        $data['name'] = $_REQUEST['name'];

        $isQuotes = $data['isQuotes'];//是否需要报价 0:不需要 1:需要
        if ($isQuotes) {
            //查询汽车报价
            $data['card_time'] = $_REQUEST['card_time'];
            $data['mileage'] = $_REQUEST['mileage'];
            $year = date('Y', strtotime($data['card_time']));
            $month = date('m', strtotime($data['card_time']));
            $carPrice = $this->modelsValuation($data['detail_model_id'], $year, $month, $data['mileage'], $data['city_id']);
        } else {
            $carPrice = array();
        }

        if ($carPrice !== false) {
            $model->sellingCar($data, $carPrice, $data['openid'], $isQuotes);
        } else {
            ajax_output(2, '参数错误', (object)array());
        }
    }

    /**
     * 更新快洗的订单
     */

    public function updateSecondHand(){
        $save['status'] = $_REQUEST['status'];
        $save['update_time'] = SYS_DATE;
        M('car_secondhand')->where(['order_id'=>$_REQUEST['orderid']])->save($save);
    }

    /**
     * 根据款型去公平价估值
     * @param $detail_model_id 车辆型号id
     * @param $year  首次上牌年份
     * @param $month 首次上牌月份
     * @param $mile  里程（万里）
     * @param $city_id 城市id
     */

    public function modelsValuation($detail_model_id, $year, $month, $mile, $city_id)
    {
        $url = 'http://openapi.gongpingjia.com/api/cars/evaluation/caryustd/';
        $city_Info = M('city')->where(array('id' => $city_id))->find();
        $name = str_replace('市', '', $city_Info['name']);
        $city_code = M('city_code')->where(array('city' => $name))->find();
        $params['d_model'] = $detail_model_id;
        $params['year'] = $year;
        $params['month'] = $month;
        $params['mile'] = $mile;
        $params['city'] = $city_code['city_code'];
        $result = commonCurl($params, $url, 0);
        $result = json_decode($result, true);
        if ($result['status'] == 'success') {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取车辆类型列表
     */

    public function getCarModelsList()
    {
        $list = M('car_library')->field('brand_id,brand_name,brand_logo')->group('brand_id')->order('brand_id asc')->select();
        ajax_output(0, '', $list);
    }

    public function getCarModelsList1()
    {
        $list = M('car_library')->field('brand_id,brand_name,brand_logo')->group('brand_id')->order('brand_id asc')->select();
        foreach ($list as $k => $v) {
            $letter .= $this->getFirstCharter($v['brand_name']) . ',';
        }
        $data = array_unique(explode(',', rtrim($letter, ',')));
        sort($data);
        unset($data[0]);
        foreach ($list as $k => $v) {
            $currentLetter = $this->getFirstCharter($v['brand_name']);
            foreach ($data as $ks => $vs) {
                if ($currentLetter == $vs) {
                    $result[0]['car'][$currentLetter][] = $v;

                }
            }
        }
        ajax_output(0, '', $result);
    }

    /**
     * 车型二级列表
     */
    public function getSunModelsList()
    {
        $brand_id = I('brand_id', '', 'trim');
        if (!empty($brand_id)) {
            $where = "brand_id=" . $brand_id;
        } else {
            ajax_output(1, '请求参数错误');
        }
        $list = M('car_library')->field('brand_id,brand_name,brand_logo,model_id,model_name,manufacturer,detail_model_id,detail_model_name')->where($where)->group('model_name')->select();
        ajax_output(0, '', $list);
    }

    /**
     * 车型三级列表
     */
    public function getChildModelsList()
    {
        $model_id = I('model_id', '', 'trim');
        if (!empty($model_id)) {
            $where = "model_id=" . $model_id;
        } else {
            ajax_output(1, '请求参数错误');
        }
        $list = M('car_library')->field('brand_id,brand_name,brand_logo,model_id,model_name,manufacturer,detail_model_id,detail_model_name')->where($where)->select();

        foreach ($list as $k => $v) {
            $year = explode(' ', $v['detail_model_name']);
            $str .= $year[0] . ',';
        }
        //对车辆数据按年份排序
        $data = array_unique(explode(',', rtrim($str, ',')));
        rsort($data);
        $result = array();
        foreach ($list as $k => $v) {
            $year = explode(' ', $v['detail_model_name']);
            $currentYear = $year[0];
            foreach ($data as $ks => $vs) {

                if ($currentYear == $vs) {
                    $result[$ks]['year'] = $vs;
                    $result[$ks]['car'][] = $v;

                }
            }

        }
        $result = $this->multisortArray($result, 'year');

        ajax_output(0, '', $result);
    }

    /**
     * 二维数组按某个元素来排序
     * SORT_DESC
     */
    public function multisortArray($arr, $need, $sort = SORT_DESC)
    {
        $flag = array();
        if ($arr) {
            foreach ($arr as $k => $arr2) {
                $flag[$k] = $arr2[$need];
            }
            array_multisort($flag, $sort, $arr);
        }
        return $arr;
    }


    public function test()
    {
        $result = $this->modelsValuation('274605', '2017', '06', '3.2', 2);
        var_dump($result);
        die;
    }

    /**
     * 订单列表
     */
    public function secondHandList()
    {
        $param = array('session_id');
        $check = array();
        $data = $this->checkParam($param, $check);
        $operator_id = $this->session_handle->getsession_userid($data['session_id']);
        $merchantModel = new \Api\Model\MerchantModel();
        $shop_id = $merchantModel->shopId($operator_id);

        $status = I('status', '', 'trim');
        $source = I('source', '', 'trim');
        $where = "1=1 and a.shop_id='$shop_id'";

        if (!empty($status) && $status > 0) {
            $where .= " and a.status='$status'";
        }
        if (!empty($source) && $source > 0) {
            $where .= " and a.source='$source'";
        }
        $list = M('car_secondhand')->alias('a')->join('LEFT JOIN car_library as l on a.detail_model_id=l.detail_model_id')->where($where)
            ->field('a.id,a.order_id,a.source,a.shop_id,a.shop_name,a.vehicle_price,a.card_time,a.mileage,a.shop_income,a.source,a.status,a.add_time,a.isQuotes,l.model_name,l.detail_model_name,l.brand_logo')->order('a.add_time desc')->select();

        foreach ($list as $k => $v) {
            $v['add_time'] = !empty(date('Y年m月d日', $v['add_time'])) ? date('Y年m月d日', $v['add_time']) : '';
            $v['card_time'] = !empty(date('Y年m月', strtotime($v['card_time']))) ? date('Y年m月', strtotime($v['card_time'])) : '';
            !empty($v['shop_income']) ? $v['shop_income'] : '';
            !empty($v['mileage']) ? $v['mileage'] : '';
            !empty($v['vehicle_price']) ? $v['vehicle_price'] : '';
            $result[] = $v;
        }

        if (empty($result)) {
            $result = array();
        }
        ajax_output(0, '', $result);
    }

    /**
     * 订单详情
     */
    public function orderInfo()
    {
        $param = array('session_id', 'order_id');
        $check = array();
        $data = $this->checkParam($param, $check);
        $operator_id = $this->session_handle->getsession_userid($data['session_id']);  //user_id
        $merchantModel = new \Api\Model\MerchantModel();
        $shop_id = $merchantModel->shopId($operator_id);

        $SecondHand = D('SecondHand');
        //获取订单信息
        $info = $SecondHand->orderInfo($data['order_id'], $shop_id);
        if (empty($info)) ajax_output(1, '您所查询的订单不存在');

        $checkRes = D('carGuaranty')->checkAddTime($info['add_time'], 'SecondHand/orderInfo');
        //获取logo
        $info['brand_logo'] = D('carGuaranty')->brand_logo($info['detail_model_id']);
        //获取品牌名称
        $info['brand_name'] = D('carGuaranty')->brand_name($info['detail_model_id']);

        if ($checkRes) {
            //订单状态更改时间
            $info['statusChangArr'] = $SecondHand->orderChangTime($data['order_id'], $info['status']);
            $info['OrderTimeCheckRes'] = 1;  //订单时间检测结果
        } else {
            $info['statusChangArr'] = D('carGuaranty')->emptyorderChangTime();
            $info['OrderTimeCheckRes'] = 0;  //订单时间检测结果
        }

        //删除无效信息
        unset($info['brand_id']);
        unset($info['city_id']);
        unset($info['id']);

        ajax_output(0, '请求成功', $info);

    }

    /**
     * 根据首字母进行排序
     * @param $str
     * @return null|string
     */

    public function getFirstCharter($str)
    {

        if (empty($str)) {
            return '';
        }

        $fchar = ord($str{0});

        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});

        $s1 = iconv('UTF-8', 'gb2312', $str);

        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;

        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;

        if ($asc >= -20319 && $asc <= -20284) return 'A';

        if ($asc >= -20283 && $asc <= -19776) return 'B';

        if ($asc >= -19775 && $asc <= -19219) return 'C';

        if ($asc >= -19218 && $asc <= -18711) return 'D';

        if ($asc >= -18710 && $asc <= -18527) return 'E';

        if ($asc >= -18526 && $asc <= -18240) return 'F';

        if ($asc >= -18239 && $asc <= -17923) return 'G';

        if ($asc >= -17922 && $asc <= -17418) return 'H';

        if ($asc >= -17417 && $asc <= -16475) return 'J';

        if ($asc >= -16474 && $asc <= -16213) return 'K';

        if ($asc >= -16212 && $asc <= -15641) return 'L';

        if ($asc >= -15640 && $asc <= -15166) return 'M';

        if ($asc >= -15165 && $asc <= -14923) return 'N';

        if ($asc >= -14922 && $asc <= -14915) return 'O';

        if ($asc >= -14914 && $asc <= -14631) return 'P';

        if ($asc >= -14630 && $asc <= -14150) return 'Q';

        if ($asc >= -14149 && $asc <= -14091) return 'R';

        if ($asc >= -14090 && $asc <= -13319) return 'S';

        if ($asc >= -13318 && $asc <= -12839) return 'T';

        if ($asc >= -12838 && $asc <= -12557) return 'W';

        if ($asc >= -12556 && $asc <= -11848) return 'X';

        if ($asc >= -11847 && $asc <= -11056) return 'Y';

        if ($asc >= -11055 && $asc <= -10247) return 'Z';

        return null;

    }

    /*
     * 获取微信用户的二手车订单列表
     */
    public function wechatCarList()
    {
        $param = array('open_id', 'page', 'row');
        $check = array();
        $data = $this->checkParam($param, $check, 1);
        $openid = $data['open_id'];
        $page = $data['page'];
        $num = $data['row'];
        $list = M('car_secondhand')->alias('c')->join('LEFT JOIN car_library as l  on c.detail_model_id=l.detail_model_id')->where("c.open_id='$openid'")->field('c.*,l.brand_logo,l.model_name,detail_model_name')->order('c.add_time desc')->limit($num)->page($page)->select();
        foreach ($list as $k => $v) {
            $v['card_time'] = date('Y年m月', strtotime($v['card_time']));
            $v['add_time'] = date('Y年m月d日', $v['add_time']);
            $result[] = $v;
        }
        if (!empty($result)) {
            ajax_output(0, '', $result);
        } else {
            ajax_output(0, '', array());
        }

    }



}