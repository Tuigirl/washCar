<?php
namespace Wechat\Controller;

use Think\Controller;

class CommonController extends Controller{

    /**
     *  参数检测与过滤
     */
    public function checkParam($param,$check = array(),$nosign=0){

        //校验sign
        if($_REQUEST['debug'] || $nosign){
            $data = $_REQUEST;
            if(empty($data)){
                $data = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
            }
        }else{
            $data = self::checkApiSign();
        }
        $rel = array();
        //过滤不合法
        $data = array_map(function ($v){
            return htmlspecialchars($v);
        }, $data);
        foreach ($param as $k =>$r){
            foreach ($data as $key =>$row){
                $isset = 0;
                if($key == $r ){
                    $isset = 1;
                    if(!empty($check[$k])){
                        $func = $check[$k];
                        $data[$r] = $func($row);
                    }
                    //如果参数不存在 或者参数为空 且不等于0 (string，int)
                    if(!isset($data[$r]) || (empty($data[$r]) &&  !($data[$r] === '0' || $data[$r] === 0) )){
                        ajax_output('1',"缺少参数 code 1:".$r);
                    }
                    break;
                }
            }
            if($isset == 0){
                ajax_output('1',"缺少参数 code 2:".$r);
            }
        }
        return $data;
    }


    /**
     * 接口签名校验
     * 去除混淆
     * 根据规则进行校验
     */
    public function checkApiSign(){
        $Json_data = $GLOBALS['HTTP_RAW_POST_DATA'];
        $item = json_decode($Json_data, true);
        if (!empty($item['sign'])) {
            $_POST = $item;
        }
        $sign = $_POST['sign'];
//        if (empty($sign)) {
//            ajax_output('3', '签名校验为空');
//        }
        for($i=1;$i<=15;$i++){
            $sign{3*$i-1}='';
        }
        $sign = preg_replace('|\0|', '', $sign);
        $sign = substr($sign, 0,32);
        $data = $_POST;
        $merchant_id = $data['merchant_id'];
        //无需校验的部分参数
        unset($data['sign']);
        unset($data['merchant_id']);
        ksort($data);
        $urlencode = "";
        foreach ($data as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $urlencode .= $k . "=" . $v . "&";
            }
        }
        $urlencode = trim($urlencode, "&");
        $str = $urlencode."&key=".C('API_CHECK_APISECRET');

        $str = strtoupper(md5($str));
        if($str == $sign){
            $data['merchant_id'] = $merchant_id;
            return $data;
        }else{
            ajax_output('3','签名校验出错');
        }
    }



    /**
     *  更新fromid
     */

     function updateFormId($openid,$fromid){

        $res =  M('fromid')->where(['openid'=>$openid])->find();
        if($res){
            M('fromid')->where(['openid'=>$openid])->save(['fromid'=>$fromid]);
        }else{
            $add['openid'] = $openid;
            $add['fromid'] = $fromid;
            M('fromid')->add($add);
        }
    }

    /**
     * 获取fromid
     */
    function getFromId($openid){
        return M('fromid')->where(['openid'=>$openid])->getField('fromid');
    }

}