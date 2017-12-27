<?php
/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    $charset = strtolower($charset);
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 检测输入的验证码是否正确
 * @param string $code 为用户输入的验证码字符串
 * @return boolen
 */
function check_verify($code, $id = '')
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

function wximg2($imgDir, $filename, $xmlstr)
{

    if (empty($xmlstr)) {
        $xmlstr = file_get_contents('php://input');
    }

    $jpg = $xmlstr;//得到post过来的二进制原始数据
    if (empty($jpg)) {
        $status = array('code' => '3', 'data' => 'nostream');
        return $status;
        exit();
    }
    $file = fopen($imgDir.$filename,"w");//打开文件准备写入
    fwrite($file,$jpg);//写入
    fclose($file);//关闭

    $filePath = $imgDir . $filename;

    //图片是否存在
    if (!file_exists($filePath)) {
        $status = array('code' => '3', 'data' => 'createFail');
        return $status;
        exit();
    } else {
        thumbImg($filePath, '1048', $filePath);  //压缩图片
        $status = array('code' => '0', 'data' => '生成二维码成功', 'url' => $filePath);
    }

    return $status;
}

/**
 * 对用户的密码进行加密
 * @param string $password
 * @param string $encrypt //传入加密串，在修改密码时做认证
 * @param string $isapp 是否是app
 * @return array/password
 */
function password($password, $encrypt = '', $isapp = '')
{
    $pwd = array();
    $pwd['encrypt'] = $encrypt ? $encrypt : Org\Util\String::randString(6);
    if ($isapp == 1) {
        $pwd['password'] = md5(strtolower(trim($password)) . $pwd['encrypt']);
    } else {
        $pwd['password'] = md5(md5(strtolower(trim($password))) . $pwd['encrypt']);
    }
    return $encrypt ? $pwd['password'] : $pwd;
}

/**
 * 生成接口签名校验
 * 添加混淆
 * 根据规则进行校验
 */
function createApiSign($data)
{
    //无需校验的部分参数
    unset($data['sign']);
    if (empty($data)) {
        return false;
    }

    ksort($data);
    $urlencode = "";
    foreach ($data as $k => $v) {
        if ($k != "sign" && $v != "" && !is_array($v)) {
            $urlencode .= $k . "=" . $v . "&";
        }
    }
    $urlencode = trim($urlencode, "&");
    $str = $urlencode . "&key=" . C('API_AUTH_APISECRET');
    $str = strtolower(md5($str));
    $sign = '';
    for ($i = 1; $i <= 32; $i++) {
        if (($i % 2) == 0) {
            $sign .= $str{$i - 1} . getRandChar(1);
        } else {
            $sign .= $str{$i - 1};
        }

    }

    return $sign;

}

function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

    return $str;
}

function checkMobile($mobile)
{
    if (preg_match("/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])\d{8}$/i", $mobile)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 输出二维码图片
 *
 * @param $content string 二维码内容
 * @param $outfile boolean|string 生成的二维码图片文件名
 * @param $outpath string 二维码图片保存路径
 * @param $logo string 中间带logo图片地址
 * @param $outprint boolean 是否图像流直接输出到浏览器
 * @param $errorCorrectionLevel string 容错级别
 * @param $matrixPointSize integer 生成图片大小, 1到10
 * @param $margin integer 二维码周围边框空白区域间距值
 */
function qr_img($content, $outfile = false, $outpath = '', $logo = '', $outprint = true, $errorCorrectionLevel = 'L', $matrixPointSize = 6, $margin = 2)
{
    vendor('phpqrcode.phpqrcode');
    $object = new \QRcode();
    $errorCorrectionLevel = $errorCorrectionLevel ? $errorCorrectionLevel : 'L'; //容错级别
    $matrixPointSize = $matrixPointSize ? $matrixPointSize : 6; //生成图片大小, 1到10
    $margin = (int)$margin; //二维码周围边框空白区域间距值
    //直接输出到浏览器
    if (!$outfile) {
        $object->png($content, $outfile, $errorCorrectionLevel, $matrixPointSize, $margin);
    } //生成保存图片
    else {
        $outpath = APP_PATH . '../Uploads/Qrcode/' . $outpath;
        $outfile = file_exists($outfile) ? $outfile : rtrim($outpath, '/') . '/' . $outfile;
        //是否已有同名文件，如有则输出文件，无需再次生成
        if (!file_exists($outfile)) {
            if (!file_exists(dirname($outfile))) {
                @mk_dirs(dirname($outfile)); //生成目录
            }
            //生成二维码图片
            $object->png($content, $outfile, $errorCorrectionLevel, $matrixPointSize, $margin);
        }

        if ($logo !== FALSE) {
            ////$logo = rtrim($outpath,'/'). '/'. $logo;
            $logo = file_exists($logo) ? $logo : APP_PATH . '../' . $logo;
            $QR = imagecreatefromstring(file_get_contents($outfile)); //已经生成的原始二维码图
            $logo = imagecreatefromstring(file_get_contents($logo)); ///准备好的logo图片
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
            //输出图片
            imagepng($QR, $outfile);
        }
        if ($outprint) {
            header("Content-type:image/png");
            $QR = imagecreatefromstring(file_get_contents($outfile)); //已经生成的原始二维码图
            imagepng($QR);
        }
        if (isset($QR)) imagedestroy($QR);
    }
}


/**
 * @param $params   携带参数
 * @param $url      第三方地址
 * @param int $type 0 = get 1 = post
 * @param int $isSSL
 * @param int $is_raw
 * @return mixed
 */
function commonCurl($params, $url, $type = 0, $isSSL = 1, $is_raw = 0)
{

    $ch = curl_init();
    if ($type == 0 && $params) {

        $url .= '?' . http_build_query($params);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
//         curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded")); //头部要送出'Expect: '
    if ($isSSL == 1) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }

    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //强制使用IPV4协议解析域名
    curl_setopt($ch, CURLOPT_POST, $type);
    if ($type == 1) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params); //post
    }
    if ($is_raw) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($params),
        ));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//抓取跳转后的页面
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);    // Timeout
    $res = curl_exec($ch);
//         $error = curl_error($ch);
//         dump($error);
    return $res;
}

/**
 * 邮件发送函数
 */
function sendMail($to, $title, $content, $file)
{
    try {
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host = C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
        $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
        $mail->SMTPSecure = 'ssl'; // 使用安全协议
        $mail->Port = C('MAIL_PORT');
        $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
        $mail->Password = C('MAIL_PASSWORD'); //邮箱密码
        $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
        $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
        $mail->AddAddress($to, "尊敬的客户");
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
        $mail->CharSet = C('MAIL_CHARSET'); //设置邮件编码
        $mail->Subject = $title; //邮件主题
        $mail->AllowEmpty = true;
        $mail->Body = $content; //邮件内容
        $mail->AltBody = ""; //邮件正文不支持HTML的备用显示
        if ($file) {
            $mail->AddAttachment($file); //可以添加附件
        }
    } catch (phpmailerException $e) {
        echo "邮件发送失败：" . $e->errorMessage();
    }
    return ($mail->Send());
}


function toGbk($row)
{
    return iconv("UTF-8", "GB2312//IGNORE", $row);
}


//创建TOKEN
function creatToken()
{
    $code = chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE));
    session('TOKEN', authcode($code));
}

//判断TOKEN
function checkToken($token)
{

    if ($token == session('TOKEN')) {
        session('TOKEN', NULL);
        return TRUE;
    } else {
        return FALSE;
    }

}

/* 加密TOKEN */
function authcode($str)
{
    $key = "ANDIAMON";
    $str = substr(md5($str), 8, 10);
    return md5($key . $str);
}

/**
 * 压缩图片
 * @param $img_path  图片地址
 * @param $thumb_w  图片宽度
 * @param $save_path
 * @param bool $is_del
 */
function thumbImg($img_path, $thumb_w, $save_path, $is_del = true)
{
    $image = new \Think\Image();
    $image->open($img_path);
    $width = $image->width(); // 返回图片的宽度
    if ($width > $thumb_w) {
        $width = $width / $thumb_w; //取得图片的长宽比
        $height = $image->height();
        $thumb_h = ceil($height / $width);
    }

    //如果文件路径不存在则创建
    $save_path_info = pathinfo($save_path);
    if (!is_dir($save_path_info['dirname'])) mkdir($save_path_info['dirname'], 0777);

    $image->thumb($thumb_w, $thumb_h)->save($save_path);
}


/**
 * 获取连续的Y-m-d 的时间
 * @param $timeStar 2017-8-28
 * @param $timeEnd 2017-9-28
 * @return array
 */
function runningDate($timeStar, $timeEnd)
{
    $begintime = strtotime($timeStar);
    $endtime = strtotime($timeEnd);
    for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
        $date[] = date("Y-m-d", $start);
    }
    return $date;
}


/**
 * ajax返回
 * 格式 code msg data
 */
function ajax_output($code = 0, $msg = '操作成功', $data = array())
{
    if ($code > 0 && empty($msg)) {
        $msg = "操作失败";
    }
    die(json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data)));
}

/**
 *  非中断 返回
 * 格式 code msg data
 */
function return_output($code = 0, $msg = '操作成功', $data = array())
{
    if ($code > 0 && empty($msg)) {
        $msg = "操作失败";
    }
    return array('code' => $code, 'msg' => $msg, 'data' => $data);
}

/**
 * 数组排序
 */
function multisortArray($arr, $need, $sort = SORT_ASC) {
    $flag = array();
    if ($arr) {
        foreach ($arr as $k => $arr2) {
            $flag[$k] = $arr2[$need];
        }
        array_multisort($flag, $sort, $arr);
    }
    return $arr;
}

/**
 * 生成订单号
 */

function getOrderId($device){
    return date('Ymd').$device.substr(time(),-1,5).rand(1111,9999);
}

/**
 * 检测数据是否为空
 */
function is_empty($data)
{
    if (!empty($data)) {
        return $data;
    }
    return '';
}


function getDevice($id){
    return M('wash_device')->where(['id'=>$id])->getField('washCar_device');
}
function getDeviceId($device){
    return M('wash_device')->where(['washCar_device'=>$device])->getField('id');
}

function deviceCityInfo($device){
    $city = M('wash_device')->where(['washCar_device'=>$device])->getField('city');
    return $city_info = M('city')->where(['id'=>$city])->find();
}
