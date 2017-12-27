<?php
$dst_path = '170005.png';
$src_path = 'shui.png';
//创建图片的实例
$dst = imagecreatefromstring(file_get_contents($dst_path));
$src = imagecreatefromstring(file_get_contents($src_path));

//获取水印图片的宽高
list($src_w, $src_h) = getimagesize($src_path);
//将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
//imagecopymerge($dst, $src, 10, 10, 0, 0, $src_w, $src_h, 100);

//如果水印图片本身带透明色，则使用imagecopy方法
 imagecopy($dst, $src, 156, 156, 0, 0, $src_w, $src_h);

//输出图片
list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);

switch ($dst_type) {
    case 1://GIF
        header('Content-Type: image/gif');
        imagegif($dst,'E:\phpstudy\WWW\Xiaochengxu\aaa.png');
        break;
    case 2://JPG
        header('Content-Type: image/jpeg');
        imagejpeg($dst,'E:\phpstudy\WWW\Xiaochengxu\aaa.png');
        break;
    case 3://PNG
        header('Content-Type: image/png');
        imagepng($dst,'E:\phpstudy\WWW\Xiaochengxu\aaa.png');
        break;
    default:
        break;
}
imagedestroy($dst);
imagedestroy($src);

