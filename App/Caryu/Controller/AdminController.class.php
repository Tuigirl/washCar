<?php

namespace Caryu\Controller;

use Caryu\Controller\CommonController;

class AdminController extends CommonController
{
    //账号列表
    public function adminList()
    {
        $authGroups = D('Auth')->getGroups();
        $authGroups = $authGroups['rows'];
        $cityInfo = getSysCity(); //获取系统开通城市
        $this->assign('cityInfo', $cityInfo);
        $this->assign('authGroups', $authGroups);
        $this->display();
    }

    public function getAdminList()
    {
        $data = D('Admin')->getAdminList();
        exit(json_encode($data, true));
    }

    public function delAdmin()
    {
        $result = M('admin')->where(array('id' => I('post.id')))->save(array('is_delete'=>1));
        M('adminAuthGroupAccess')->where(array('uid' => I('post.id')))->delete();
        M('admin_city')->where(array('uid' => I('post.id')))->delete();
        if ($result) $this->success('删除成功');
        else $this->error('删除失败');
    }

    public function saveAdmin()
    {
        $id = I('post.id', 0, 'intval');
        $data['user_name'] = I('post.user_name', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $pwd = I('post.pwd', '', 'trim');
        $create_time = I('post.create_time', '', 'trim');
        $data['create_time'] = date('Y-m-d H:i:s', time());
        $group = I('post.group', 0, 'intval');
        $city_id=I('post.city_id');
        $city_name=I('post.city_name');
        if (!$group) $this->error('分组为空');
        if (count($city_id) > 0) {
            $city['city_id'] = implode(',', $city_id);
            $city['city_name'] =implode(',', $city_name);


        }

        if ($id) {
            if (!empty($pwd)) {
                $encrypt = M('admin')->where(array('id' => $id))->getField('encrypt');
                $data['pwd'] = md5(md5($pwd) . $encrypt);
            }
            $result = M('admin')->where(array('id' => $id))->save($data);
            if ($result){
                M('adminAuthGroupAccess')->where(array('uid' => $id))->save(array('group_id' => $group));
                $adminCity= M('admin_city')->where(array('uid' => $id))->find();
                $city['uid']=$id;
                if(empty($adminCity)){
                    M('admin_city')->add($city);
                }else{
                    M('admin_city')->where(array('uid' => $id))->save($city);
                }


            }
            //echo M('adminAuthGroupAccess')->_sql();
        } else {
            $encrypt = $this->encryptfactory();
            $data['encrypt'] = $encrypt;
            $data['pwd'] = md5(md5($pwd) . $encrypt);
            $result = M('admin')->add($data);
            if ($result){
                M('adminAuthGroupAccess')->add(array('group_id' => $group, 'uid' => $result));
                $city['uid']=$result;
                M('admin_city')->add($city);
            }

        }
        if ($result !== false) $this->success('操作成功');
        else echo M('admin')->_sql();
    }

    private function encryptfactory()
    {
        $str = '';
        for ($i = 1; $i <= 6; $i++) {
            $str .= chr(rand(97, 122));
        }
        return $str;
    }
}