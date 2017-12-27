<?php
namespace Xiaochengxu\Controller;

class CarInfoController extends CommonController
{

    public $path = 'http://test.caryu.com/index.php/CaryuApi/CarInfo/';
    public $is_return = 0;

    /**
     * 获取车辆的基本信息
     */
    public function getCarInfo()
    {
        if (empty($_REQUEST['license_number'])) ajax_output(1, '缺少参数license_number');
        $url = $this->path . 'getCarType';
        $res = commonCurl($_REQUEST, $url);
        $res = json_decode($res,true);

        //获取上次投保的公司名字
        if($res['data']['lastRenewal']){
            $res['data']['lastInsuranceCompany'] = C('AllInsurance')[$res['data']['lastRenewal']];
        }
        $res = json_encode($res);
        if ($this->is_return) {
            return $res;
        } else {
            echo $res;
        }
    }

    /**
     * 获取违章
     */

    public function getCarViolation()
    {
        if (empty($_REQUEST['license_number'])) ajax_output(1, '缺少参数license_number');
        $url = $this->path . 'carViolation';
        $res = commonCurl($_REQUEST, $url);

        if ($this->is_return) {
            return $res;
        } else {
            echo $res;
        }
    }

    /**
     * 获取估值
     */

    public function getEvalPrice()
    {
        if (empty($_REQUEST['license_number'])) ajax_output(1, '缺少参数license_number');
        $_REQUEST['city_id'] = '1326';
        $url = $this->path . 'getEvalPrice';
        $res = commonCurl($_REQUEST, $url);
        if ($this->is_return) {
            return $res;
        } else {
            echo $res;
        }
    }


    /**
     * 获取打印单信息
     */

    public function printData()
    {
        if (empty($_REQUEST['license_number'])) ajax_output(1, '缺少参数license_number');
        $this->is_return = 1;
        //获取违章
        $carVionlation = $this->getCarViolation();
        $data['carVionlation'] = json_decode($carVionlation, true)['data'];

        //获取估值
        $CarValuation = $this->getEvalPrice();
        $data['CarValuation'] = json_decode($CarValuation, true)['data'];

        //获取车险
        $carInsurance = $this->getCarInfo();
        $data['carInsurance'] = json_decode($carInsurance, true)['data'];

        ajax_output(0, 'success', $data);
    }

    /**
     * 违章支付成功
     */

    public function carViolationPaySuccess($orderid)
    {
        $params['pay_type'] = 2;
        $params['orderid'] = $orderid;

        $url = $this->path . 'carViolationPaySuccess';
        $res = commonCurl($params, $url);
        if ($this->is_return) {
            return $res;
        } else {
            echo $res;
        }
    }

    /**
     * 将二手车的订单发送到驾遇
     */

    public function sendSecondHandToCaryu($data){
        $url = $this->path . 'saveSecondHand';
        $res = commonCurl($data, $url);
        if ($this->is_return) {
            return $res;
        } else {
            echo $res;
        }
    }

}