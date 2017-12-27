<?php
namespace Caryu\Model;
use Think\Model;
class CommonModel extends Model{
	//添加序号
	public function dataAddSortId($data,$page,$rows) {
		$num = ($page - 1) * $rows;
		foreach ($data as $key => $value) {
			$data[$key]['sortId'] = $num + $key +1;
		}
		return $data;
	}

    /**
     * 检测用户请求的城市是否符合规范
     * $city_id  为空表示查找该账号的所有城市
     * return   返回城市2，1326表示有权限  0无权限
     */
    public function checkRequestCity($city_id){
        $user_id = cookie('saas_admin_userid');

        if($city_id){

            $where['uid'] = $user_id;
            $res = M('admin_city')->where($where)->getField('city_id');
            $arr = explode(',',$res);

            if(in_array($city_id,$arr)){
                return $city_id;
            }else{
                return 0;
            }
        }else{
            $city_id = M('admin_city')->where(array('uid'=>$user_id))->getField('city_id');
            if($city_id) return $city_id;
            else return 0;
        }
    }


}