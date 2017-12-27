<?php
namespace Wechat\Controller;

class ClearCarController extends CommonController
{
    private $rocord;

    public function __construct()
    {
        $this->rocord =  D('ClearRecord');
    }






}
