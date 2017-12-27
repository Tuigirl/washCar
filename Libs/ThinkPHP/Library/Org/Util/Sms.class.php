<?php
/**
* 发送短信
*/
namespace Org\Util;
class Sms {
    public static $platfrom; //发送短信平台 1-> 互亿无限2 ->深圳君诚信鸽平台 3 ->云测试  其他：->创蓝

    public function __construct() {
        self::$platfrom = $platfrom;
    }

     /**
      * 发送短信
      * @param string $mobile 手机号码
      * @param int $type 短信模板 type
      * @param int $ty 0.用户端  2.商务端 3.web用户    发送者
      * @param int $ext 验证码的长度 0.默认6位  1.4位
      * @param array $extData 附加数据
      * @param int $saveToDb 是否需要写入数据库 0.不需要  1.需要  ，默认需要（验证码、登录的短信，需要记录到数据库）
      * @return multitype:array
      */
	function send_sms($mobile,$type,$ty,$ext=0,$extData=array(),$saveToDb=1){
		
		if(empty($mobile)){
			$return = array('code'=>404,'msg'=>'手机号码为空');
	   		return  $return;
		}
		if(empty($type)){
			$return = array('code'=>404,'msg'=>'未选定短信模板');
	   		return  $return;
		}
// 	   	if(!in_array($ty, array('0','2','3'))){
// 	   		$return = array('code'=>404,'msg'=>'类型不符合');
// 	   		return  $return;
// 	   	}
	
      
	    switch ($type) {
          case  1://注册验证码
          case  2://验证验证码
          	if($ext == 1){
          		$mobile_code = rand(1000,9999);
          	}elseif($ext == 0){
          		$mobile_code = rand(100000,999999);
          	}
             $content="您的验证码是".$mobile_code."。感谢您使用！";
            break;
          case 3://用户选定商家后，短信提示用户
          	 $content ="温馨提示，您在驾遇上预约的(".$extData['server'].")服务成功。店名：".$extData['merchant_name']."，地址：".$extData['addr']."，请您提前安排。";
         	 break;
          case 4://到预约前半小时 短信提醒
         	 $content ="温馨提示，您在驾遇上预约的(".$extData['server'].")还有30分钟就到预约时间了，请您提前安排。店名".$extData['merchant_name']."，地址：".$extData['addr'];
         	 break;
         case 5://商家奖励运营额 打入钱包短信提醒
         	 $content ="温馨提醒：尊敬的".$extData['merchant_name']."，营业额流水奖励".$extData['money']."元已通过驾遇平台向您钱包转入，请关注到账情况。";
         	 break;
         case 6://后台提现成功 短信提醒
         	 $content ="温馨提醒：尊敬的".$extData['merchant_name']."，您在驾遇平台提现".$extData['money']."元已通过建设银行向您尾号为".$extData['card']."的账号汇入，请关注到账情况。";
         	 break;
		 case 7://车险下单成功 短信提醒
                     if($extData['open_city']==1){
                        $content="【驾遇网】尊敬的".$extData['license_number']."车主：您的保单正在出单，自即日起约1个工作日内会邮寄至".$extData['mail_addr']."，收件人".$extData['mail_name']."，手机号码".$extData['mail_phone']."。[驾遇]";
                     }else{
                         $content="【驾遇网】尊敬的".$extData['license_number']."车主：您的保单正在出单，自即日起1个工作日左右会出单完成，届时保单会通过邮件方式发送到邮箱".$extData['mail_box']."，如有疑问请联系400-889-2992。[驾遇]";
                     }
			
			 break;
	     }
     
	     $session_id = '';
         ////短信平台: 互亿无限
	     if(self::$platfrom == 1) {
             Vendor("Sms.sms");
             $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
             $post_data = "account=cf_rhkj&password=rihoukeji157&mobile=" . $mobile . "&content=" . rawurlencode($content);
             //密码可以使用明文密码或使用32位MD5加密
             $gets = xml_to_array(Post($post_data, $target));
             if ($gets['SubmitResult']['code'] == 2) {
                 if ($saveToDb) {
                     $session_id = $this->sendMsgDone($mobile, $mobile_code, $content, $ty);
                 }
                 $code = 2;
             } else {
                 $code = 1;
             }
             $return = array('code' => 0, 'msg' => $gets['SubmitResult']['msg'], 'session_id' => $session_id);

         }
         ////短信平台: 深圳君诚信鸽平台
         else if(self::$platfrom == 2) {
             $post_url = 'http://www.jc-chn.cn/smsSend.do';
             $uname = 'rhwl'; //用户名
             $pwd = 'qg645wdc'; //密码
             $post_data = array(
                'username'=> $uname,
                'password'=> md5($uname.md5($pwd)), //MD5(username+MD5(pwd))
                'mobile'=> $mobile, //手机号，多个手机号为用半角 , 分开
                'content'=> $content, //发送内容
                'dstime'=>'', //定时时间，为空时表示立即发送（选填） 格式：yyyy-MM-dd HH:mm:ss
                'ext'=>'', //用户自定义扩展（选填） 需要和后台人员确认权限
                'msgid'=>'', //客户自定义消息id（选填）
                'msgfmt'=>'', //提交消息编码格式（选填）（UTF-8/GBK）置空时默认是UTF-8
             );
             $result = remote_request($post_url, $post_data, 'post', 'utf-8');
             if ($result > 0) {
                 if ($saveToDb) {
                     $session_id = $this->sendMsgDone($mobile, $mobile_code, $content, $ty);
                 }
                 $code = 2;
                 $msg = '成功';
             } else {
                 $code = 1;
                 $msgData = array(
                     '0'=>'失败',
                     '-1'=>'用户名或者密码不正确',
                     '-2'=>'必填选项为空',
                     '-3'=>'短信内容0个字节',
                     '-4'=>'0个有效号码',
                     '-5'=>'余额不够',
                     '-10'=>'用户被禁用',
                     '-11'=>'短信内容超过500字',
                     '-12'=>'无扩展权限（ext字段需填空）',
                     '-13'=>'IP校验错误',
                     '-14'=>'内容解析异常',
                     '-990'=>'未知错误',
                 );
                 $msg = $msgData[$result] ? $msgData[$result] : '失败';
             }
             $return = array('code'=>0,'msg'=>$msg,'session_id'=>$session_id);

	     }
         //掌中信息平台webService(云测试)
         else if(self::$platfrom == 3) {
         	$url='http://api.sms.testin.cn/sms';
         	$data['apiKey'] = 'ba13e91089812056885c94a2d0759696';
         	$data['content'] = $content;
         	$data['op'] = "Sms.send";
         	$data['phone'] = $mobile;
         	$data['templateId'] = 1093;
         	$data['ts'] = floor((microtime(true)*1000));
         	
         	$str = '';
         	foreach ($data as $k => $v)
         	{
         		$str .= $k.'='.$v;
         	}$str .= '6DACE28095C5E761';
         	$data['sig'] = md5($str);
         	$params = json_encode($data);
         	
         	$ch = curl_init();
         	curl_setopt($ch, CURLOPT_URL,$url);
         	curl_setopt($ch, CURLOPT_POST, true);
         	curl_setopt($ch, CURLOPT_VERBOSE, '1');
         	
         	$user_agent = "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)";
         	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($params)) );
         	
         	curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
         	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
         	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
         	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//抓取跳转后的页面
         	curl_setopt($ch, CURLOPT_TIMEOUT, 25);    // Timeout
         	
         	$return = curl_exec($ch);
         	$return_array['STATUS'] = curl_getinfo($ch);
         	$return_array['ERROR']  = curl_error($ch);
         	$return_array['ERRNO'] = curl_errno($ch);
         	curl_close($ch);
         	if($return_array['ERRNO'] > 0 || $return_array['ERROR'])
         	{
         		$log_data = array(
         				'POSTFIELDS' => $params,
         				'return' =>var_export($return_array,TRUE)."\r\n"
         		);
         		$code = 1;
         		$msg = json_encode($log_data);
         	}else{
				$rel = json_decode($return,true);
				if($rel['code'] == 1000){
					if ($saveToDb) {
						$session_id = $this->sendMsgDone($mobile, $mobile_code, $content, $ty);
					}
					$code = 2;
					$msg = 'ok';
				}else{
					$err = array(
							'999'=>'服务启动中',
							'3002'=>'无效的参数（apiKey）',
							'3003'=>'模版id不存在',
							'3004'=>'变量数据与模版不匹配',
							'3005'=>'超过50个变量标签',
							'3008'=>'手机号不合法',
							'3009'=>'数字签名错误',
							'3010'=>'数字签名为空',
							'9999'=>'其他错误',
							'10000'=>'服务器异常',
							'10010'=>'请求的报文超过限制',
							'10011'=>'无效参数ts',
							'10012'=>'无效接入方',
							'10014'=>'账户余额不足',
							'10016'=>'短信内容不合法',
							);
					$code = 1;
					$msg = $err[$rel['code']];
					
				}        		
         	}
         	
         	
             $return = array('code'=>0,'msg'=>$msg,'session_id'=>$session_id);

         }
         ////短信平台: 创蓝
         else{
	     	$target="http://222.73.117.158/msg/HttpSendSM?";
			$post_data = "account=xmrhwl&pswd=Tch123456&mobile=$mobile&msg=$content&needstatus=true&product=";
			$gets = file_get_contents ( $target . $post_data );
			$ret = str_replace ( '\n', ',', json_encode ( $gets ) );
			$retArr = explode ( ',', $ret );
			if($retArr[1] == 0){
				if($saveToDb){
					$session_id = $this->sendMsgDone($mobile,$mobile_code,$content,$ty);
				}
				$code = 2;
			}else{
				$code = 1;
			}
		    $return = array('code'=>0,'msg'=>$retArr[1],'session_id'=>$session_id);
	     	
	     }
	     return  $return;
	    
    }
    
     public function getVerifyCode($session_id,$phone){
     	$db = M('Verifycode');
     	$data = $db ->where(array('session_id'=>$session_id,'mobile'=>$phone))->field('code,expire')->order('addtime desc')->find();
     	if(time()-$data['expire'] < 61){
     		return $data['code'];
     	}else{
     		return "";
     	}
     }
     
     public function useredCode($session_id,$phone){
     	$db = M('Verifycode');
     	$db ->where(array('session_id'=>$session_id,'mobile'=>$phone))->save(array('expire'=>time()));
     }
     
     public function sendMsgDone($mobile,$mobile_code='0',$content,$ty){
     	//发送短信成功
     	$session_id = md5(uniqid(rand()));
     	$db = M('Verifycode');
     	$data ['mobile']= $mobile;
     	$data ['type'] = $ty;
     	$data ['session_id'] = $session_id;
     	$data ['addtime'] = time();
     	$data ['expire'] = time()+C('MESSAGE_EXPIRE_TIME');
     	$data ['code'] = $mobile_code;
     	$data ['content'] = $content;
     	
     	$data = $db ->add($data);
		
     	if(!$data){
     		$session_id = md5(uniqid(rand()));
     		$data ['session_id'] =$session_id;
     		$db->add($data);
     		 
     	}
     	return $session_id;
     }
   

}

?>