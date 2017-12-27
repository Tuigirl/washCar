<?php
defined('THINK_PATH') or exit();
define('G_HTTP_HOST', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
define('G_HTTP', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
define("WEB_PATH", dirname(G_HTTP . G_HTTP_HOST . $_SERVER['SCRIPT_NAME']));
$web_path = str_replace('http://','https://',WEB_PATH);
return array(
    //微信配置参数
    'weixin_config'=>array(
        'appid' 	=> 	'wx8df705dff41be223',
        'mch_id'	=>	'1494695332',				//填写微信支付分配的商户号
        //填写微信支付结果回调地址
        'notify_url'=>  $web_path.'/index.php/Wechat/WxPay/mNotifyurl',
        'key'		=>	'416fdd5905a23e763e47184b74e6461c'				    //填写  商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    ),

    'washCar' =>[
        'APPID' => 'wx8df705dff41be223',
        'AppSecret' => 'ce6b65e9284c20805f2237bcbb74ff3e',
        //消息模板 ----- 洗车成功通知
        'paySuccess' => 'FFSrTNAKflkgeOkeizqnpeIP4PtH-aDCKNwX4Afmz94',
        //违章支付成功通知
        'carViolationPaySuccess' => 'Mgm5_Zb6mvHLrXXvqpzBLB32A4x-HniWS-tPZ-ngBOQ',
    ]

);