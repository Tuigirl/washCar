<?php
namespace Wechat\Model;

use Think\Model;

//友洗自助洗车机
class YouXiModel extends Model{

    //第三方的请求地址
    protected $url = 'http://admin.youxi1027.com/baas/washcar/washcar/washcarServlet';

    //分配的唯一标识
    protected $SHID = 'C7C692FDC8700001FADF80901E31DD50';


    /**
     * 获取token
     */
    public function getToken(){
        $params['BusiType'] = 1000;
        $params['SHID'] = $this->SHID;
        $data = commonCurl($params,$this->url);
        $token = $this->checkData($data);
        return $token['message'];
    }

    /**
     *  设备认证：检测设备是否正常
     */
    public function deviceSerialCA($DeviceSerial){
        $params['Token'] = $this->getToken();
        $params['SBNO'] = $DeviceSerial;
        $params['SHID'] = $this->SHID;
        $params['BusiType'] = 1001;
        $data = commonCurl($params,$this->url);
        return $this->checkData($data);
    }


    /**
     * 设备状态查询：查设备是否正在工作
     */
    public function deviceSerialStatus($DeviceSerial){
        $params['Token'] = $this->getToken();
        $params['SBNO'] = $DeviceSerial;
        $params['SHID'] = $this->SHID;
        $params['BusiType'] = 1002;
        $data = commonCurl($params,$this->url);
        return $this->checkData($data);
    }


    /**
     * 设备启动请求
     */
    public function deviceSerialStar($DeviceSerial){
        $params['Token'] = $this->getToken();
        $params['SBNO'] = $DeviceSerial;
        $params['SHID'] = $this->SHID;
        $params['TRANCENO'] = 0;
        $params['BusiType'] = 2001;
        $data = commonCurl($params,$this->url);
        return $this->checkData($data);
    }


    /**
     * 检测返回结果
     */
    public function checkData($jsonData){

        $data = json_decode($jsonData,true);

        if($data['status']){
            file_put_contents('./errorLog/washCar/'.date('Y-m-d').'.txt',$jsonData); //记录错误信息
        }
        return $data;
    }

    /**
     * 在支付前或者扫码前检测设备是否满足使用条件
     */

    public function checkDevice($DeviceSerial){
        //check device is ok
        $res = $this->deviceSerialCA($DeviceSerial);
        if ($res['status']) return $res;

        //check device is work
        $res = $this->deviceSerialStatus($DeviceSerial);
        if ($res['status']) return $res;

        return ['status'=> 0];
    }

    /**
     * 获取洗车的设备
     */

    public function washDevice(){
        $device = D('wash_device')->field('washCar_device')->select();
        $arr = [];
        foreach ($device as $key => $value){
            $arr[] = $value['washCar_device'];
        }
        return $arr;
    }

}