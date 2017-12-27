<?php
namespace Xiaochengxu\Controller;

class ClearCarController extends CommonController
{
    private $rocord;

    public function __construct()
    {
        $this->rocord =  D('ClearRecord');
    }


    /**
     * 扫描二维码发送洗车请求
     */
    public function toClear(){
        $param = array('DeviceSerial');
        $check = array();
        $data = $this->checkParam($param, $check);

        //获取设置本次订单金额

    }



}
