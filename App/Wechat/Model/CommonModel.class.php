<?php
namespace Wechat\Model;

use Think\Model;

class CommonModel extends Model{

    public $wechat;


    public function __construct()
    {
        $this->wechat = D('Wechat/Model');
    }

}