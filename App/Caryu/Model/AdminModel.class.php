<?php
namespace Caryu\Model;
use Think\Model;

class AdminModel extends Model{
    protected $tableName = 'admin';
	protected $pk        = 'id';
	public    $error;
    public    $status;

	/**
	 * 登录验证
	 */
	public function login($username, $password){
	    $times_db = M('times');
        //查询帐号
        $r = $this->where(array('user_name'=>$username,'is_delete'=>0))->find();
        if(!$r){
            $this->error = '管理员不存在';
            $this->status = 1;
            return false;
        }

	    //密码错误剩余重试次数
        $rtime = $times_db->where(array('username'=>$username, 'isadmin'=>'1'))->find();
        if($rtime['times'] >= C('MAX_LOGIN_TIMES')) {
            $minute = C('LOGIN_WAIT_TIME') - floor((SYS_TIME-$rtime['logintime'])/60);
            if ($minute > 0) {
                $this->error = "密码重试次数太多，请过{$minute}分钟后重新登录！";
                return false;
            }else {
                $times_db->where(array('username'=>$username, 'isadmin'=>'1'))->delete();
            }
        }

   		$password = md5(md5($password).$r['encrypt']);
        $ip = get_client_ip();
        if($r['pwd'] != $password) {
            if($rtime && $rtime['times'] < C('MAX_LOGIN_TIMES')) {
                $times = C('MAX_LOGIN_TIMES') - intval($rtime['times']);
                $times_db->where(array('username'=>$username))->save(array('ip'=>$ip,'isadmin'=>1));
                $times_db->where(array('username'=>$username))->setInc('times');
            } else {
                $times_db->where(array('username'=>$username,'isadmin'=>1))->delete();
                $times_db->add(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
                $times = C('MAX_LOGIN_TIMES');
            }
//            $this->error = "密码错误，您还有{$times}次尝试机会！";
            $this->error = "密码错误";
            $this->status = 2;
            return false;
        }
        $times_db->where(array('username'=>$username,'isadmin'=>'1'))->delete();
        $this->where(array('id'=>$r['id']))->save(array('lastloginip'=>$ip,'lastlogintime'=>Date('Y-m-d H:i:s')));

        //session
        session('saas_admin_userid', $r['id']);
        session('saas_admin_username', $username);
        //cookie
        cookie('saas_admin_userid', $r['id']);
        cookie('saas_admin_username', $username);

         return true;
	}

	/**
	 * 获取用户信息
	 */
// 	public function getUserInfo($userid){
// 	    $admin_role_db = D('AdminRole');
// 	    $info = $this->field('password, encrypt', true)->where(array('userid'=>$userid))->find();
// 		if($info) $info['rolename'] = $admin_role_db->getRoleName($info['roleid']);    //获取角色名称
// 	    return $info;
// 	}

	/**
	 * 修改密码
	 */
	public function editPassword($userid, $password){
		$userid = intval($userid);
		if($userid < 1) return false;
		$passwordinfo = password($password);
		return $this->where(array('id'=>$userid))->save($passwordinfo);
	}
    public function getAdminList() {

        $data = array();
        $data['rows'] = M('admin')->where(array('is_delete'=>0))->select()?:false;
        foreach ($data['rows'] as $key => $value) {
            $groupId = M('adminAuthGroupAccess')->where(array('uid'=>$value['id']))->getField('group_id');
            if($groupId){
                $data['rows'][$key]['groupId'] = $groupId;
                $data['rows'][$key]['groupTitle'] = M('adminAuthGroup')->where(array('id'=>$groupId))->getField('title');
            }
            $city_Info=M('admin_city')->where(array('uid'=>$value['id']))->find();
            $data['rows'][$key]['city_id'] = explode(',',$city_Info['city_id']);
            $data['rows'][$key]['city_name'] = explode(',',$city_Info['city_name']);
        }
        $data['total'] = M('admin')->count()?:0;
        return $data;
    }
}