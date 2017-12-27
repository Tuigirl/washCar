<?php
namespace Xiaochengxu\Controller;

class ConfigController extends CommonController
{

    private $baseUrl = 'https://washcar.caryu.com/index.php/Xiaochengxu';

    /**
     * 获取button链接
     */

    public function getButtonUrl()
    {
        $param = array('orderid', 'license_number');
        $check = array();
        $data = $this->checkParam($param, $check, $nosign = 1);

        $orderInfo = M('wash_order')->where(['order_id' => $data['orderid']])->find();
        $button = $this->buttonInfo($data['license_number'], $orderInfo['openid'], $orderInfo['device']);
        ajax_output(0, 'success', $button);
    }

    /**
     * 控制小程序的四个按钮及链接
     */

    public function buttonInfo($license_number, $openid, $device)
    {
        $data = [
            //违章
            'carViolation' => [
                'is_show' => 1,
                'url' => $this->baseUrl . '/CarViolation/violationList?license_number=' . $license_number . '&openid=' . $openid . '&device=' . $device,
            ],
            //车险
            'carInsurance' => [
                'is_show' => 0,
                'url' => $this->baseUrl
            ],
            //二手车
            'sencondHand' => [
                'is_show' => 1,
                'url' => $this->baseUrl . '/Common/?license_number=' . $license_number . '&openid=' . $openid . '&device=' . $device . '#/CarUsed',
            ],
            //车抵押
            'carGuaranty' => [
                'is_show' => 0,
                'url' => $this->baseUrl
            ]
        ];
        return $data;
    }
}