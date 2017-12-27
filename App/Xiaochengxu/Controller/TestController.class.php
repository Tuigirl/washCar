<?php
namespace Xiaochengxu\Controller;

class TestController extends CommonController
{

    /**
     * 获取上传的图片
     */

    public function getUploadPic($id = 170005)
    {
        $dir = "/data/htdocs/Wechat/Public/upload/washCar/" . $id . "/";
        $file = scandir($dir);
        unset($file[0]);
        unset($file[1]);
        $url = 'https://washcar.caryu.com/Public/upload/washCar/170005/';
        foreach ($file as $key => $value) {
            $tmp = $url . $value;
            echo '<image src = ' . $tmp . ' width = 800px height = 800px>';
            echo "<br/>";
        }
    }

    /**
     * 设置红包
     */

    public function new_washer()
    {
        $config = [
            'on_off' => 1,
            "redPacket" => [
                [
                    'money' => 5.99,
                    'pass_day' => 0,
                    'name' => '1分洗车券',
                    'days' => 30
                ],
                [
                    'money' => 3,
                    'pass_day' => 0,
                    'name' => '3元代金券',
                    'days' => 30
                ],
                [
                    'money' => 2,
                    'pass_day' => 0,
                    'name' => '2元代金券',
                    'days' => 30
                ],
            ],
            'remark' => '新用户'
        ];
        echo json_encode($config);
    }


    public function king(){

        echo 111;
    }

}