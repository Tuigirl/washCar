<?php
namespace Caryu\Model;
use Think\Model;
/**
 * 卡种类型模型
 * @author Vania
 *
 */
class MemberModel extends CommonModel{
	protected $tableName = 'work_type';
	protected $pk        = 'id';
	public    $error;

	/**
	 * 工种列表
	 * @param unknown $page
	 * @param unknown $rows
	 */
	public function getGongZhongList($page,$rows){

		$data=$this->limit($page,$rows)->order('sort_idx ')->select();

		if (!is_array($data)){

			$data = array();
		}

		return $data;

	}
	/**
	 * 工种列表总数
	 */
	public function getGongZhongListNum(){

		$data=$this->order('sort_idx ')->select();

		if (!is_array($data)){

			$data = array();
		}

		return $data;
	}
	/**
	 * 员工列表
	 */
	public function getMemberList($sts,$page,$rows){

		$data=$this->table('operator o')->where('o.type=1 and o.sts='.$sts)->join('LEFT JOIN shop s on o.shop_id=s.id')->join('LEFT JOIN work_type w on o.work_type_id=w.id')
				->field('o.id,o.user_no,o.user_name,o.id_card,o.mobile,o.create_time,s.shop_name,o.sys_flag,w.`name`,o.shop_id,o.work_type_id,o.leave_time')->order('o.id desc')->limit($page,$rows)->select();
       
		if (!is_array($data)){

			$data = array();
		}

		return $data;
	}
	/**
	 * 员工列表总数
	 */
	public function getMemberListNum($sts){
		$data=$this->table('operator o')->where('o.type=1 and o.sts='.$sts)->join('LEFT JOIN shop s on o.shop_id=s.id')->join('LEFT JOIN work_type w on o.work_type_id=w.id')
				->field('o.id,o.user_no,o.user_name,o.id_card,o.mobile,o.create_time,s.shop_name,o.sys_flag,w.`name`,o.shop_id,o.work_type_id,o.leave_time')->order('o.id ')->select();

		if (!is_array($data)){

			$data = array();
		}

		return $data;
	}
	/**
	 * 管理员列表
	 * @param unknown $page
	 * @param unknown $rows
	 * @return multitype:
	 */
	public function getManagerList(){
        $cityId=$this->checkRequestCity();
		$page = I('page');
		$rows = I('rows');
                $sql = '1';
		if(I('mobile') ){
		    $sql.=' and o.mobile like "%'.I('mobile').'%"';
		}
		if(I('BD_id')){
            $BD_id=I('BD_id');
		    $sql .= ' and o.id ='.$BD_id.'';
		}
                $is_delete=I('is_delete');
                if($is_delete!=''){
		    $sql .= ' and o.sts ="'.$is_delete.'"';
		}

                $category=I('category');
                if($category!=''){
		    $sql .= ' and o.category ="'.$category.'"';
		}
                if(I('city_id') ){
		    $sql.=' and o.city_id = "'.I('city_id').'"';
		}
        if(!empty($cityId)){
            $sql=$sql. " and o.city_id in ($cityId)";
        }

		$data = $this->table('operator o')->join('city as c on c.id = o.city_id','LEFT')->where($sql)
		->field('c.name as city_name,o.departure_time,o.id,o.user_no,o.user_name,o.mobile,o.create_time,o.status,o.category,o.sts')->order('o.id ')->page($page)->limit($rows)->select()?:array();
                $total = $this->table('operator o')->where($sql)->count()?:0;
		//echo M('')->_sql();
		$result['total'] = $total;
		$result['rows'] = $data;
//		var_dump($data);
		exit(json_encode($result));
	}
}