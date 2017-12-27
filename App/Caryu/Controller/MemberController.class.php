<?php
namespace Caryu\Controller;
use Caryu\Controller\CommonController;
/**
 * 员工管理模块
 * @author Vania
 *
 */
class MemberController extends CommonController {
	public function MemberList(){
		$this->display();
	}
	public function Manager(){
        $cityInfo = $this->getUserCitys(); //获取系统开通城市
        $this->assign('cityInfo',$cityInfo);
        $this->display();
	}
	/**
	 * 离职员工管理页面
	 */
	public function LiZhiMemberList(){

		$this->display();
	}
	/**
	 * 工种
	 */
	public function GongZhong(){
		$this->display('GongZhongSetUp');
	}
	/**
	 * 工种列表
	 */
	public function getGongZhongList(){
		$type_model=D('Member');
		$page=$_REQUEST['page'];
		$rows=$_REQUEST['rows'];
		if ($page && $rows){
			$page=($page-1)*$rows;
		}
		$data = $type_model->getGongZhongList($page,$rows);
		$num= $type_model->getGongZhongListNum();
		$count=count($num);
		$json_string = json_encode($data);
		echo '{"total":'.$count.',"rows":' . $json_string . '}';
	}
	/**
	 * 保存工种
	 */
	public function saveGZ(){
		$Dao=M('work_type');
		$data['id'] = I('post.id', '', 'trim') ;
		$data['name'] = I('post.name', '', 'trim') ;
		$data['remarks'] = I('post.remarks', '', 'trim') ;
		$data['acc_id'] = cookie('acc_id') ;
		$data['sort_idx'] = I('post.sort_idx', '', 'trim') ;
		if($data['id']){
			$result=$Dao->where('id='.$data['id'])->save($data);
		}else{
			$result=$Dao->add($data);
		}
		echo $result;
	}
	/**
	 * 删除工种
	 */
	public function removeGZ(){
		$Dao=M('work_type');
		$id_array = $_POST['ids'];
		for($i=0;$i<count($id_array);$i++){
			$array[$i]=json_decode($id_array[$i],true);
			$result=$Dao->delete($array[$i]['id']);
		}
		echo $result;
	}
	/**
	 * 工种下拉框
	 */
	public function getwork_type(){
		$shop_db = M('work_type');
		$data = $shop_db->select();
		$str='[{
		                        "id":"0",
					             "text":"请选择"
						 } ,';
		$j=1;
		for ($i=0;$i<count($data);$i++){
			if($j>1){
				$str=$str.',{
		                        "id":"'.$data[$i]['id'].'",
					             "text":"'.$data[$i]['name'].'"
						 } ';
			}else{
				$str=$str.'{
		                        "id":"'.$data[$i]['id'].'",
					             "text":"'.$data[$i]['name'].'"
						 } ';
			}
			$j++;
		}
		echo $str."]";
	}
	/**
	 * 员工列表
	 */
	public function getMemberList(){
		$type_model=D('Member');
		$page=$_REQUEST['page'];
		$rows=$_REQUEST['rows'];
		if ($page && $rows){
			$page=($page-1)*$rows;
		}
		$sts = I('get.sts', '', 'trim') ;
		$data = $type_model->getMemberList($sts,$page,$rows);
		$num= $type_model->getMemberListNum($sts);
		$count=count($num);
		$json_string = json_encode($data);
		echo '{"total":'.$count.',"rows":' . $json_string . '}';
	}
	/**
	 * 管理员列表
	 */
	public function getManagerList(){

		D('Member')->getManagerList();
	}
	/**
	 * 保存员工
	 */
	public function saveMember(){
		$Dao=M('operator');
		$data['id'] = I('post.id', '', 'trim') ;
		$data['user_no'] = I('post.user_no', '', 'trim') ;
		$data['user_name'] = I('post.user_name', '', 'trim') ;
		$data['mobile'] = I('post.mobile', '', 'trim') ;
		$data['id_card'] = I('post.id_card', '', 'trim') ;
		$data['shop_id'] = I('post.shop_id', '', 'trim') ;
		$data['work_type_id'] = I('post.work_type_id', '', 'trim') ;
		$data['type'] = I('post.type', '', 'trim') ;
		$data['sts'] = I('post.sts', '', 'trim') ;
		$data['sys_flag'] = I('post.sys_flag', '', 'trim') ;
		$data['create_time'] = Date('Y-m-d H:i:s')  ;
		$data['acc_id'] = session('acc_id');
		if($data['id']){
			$result=$Dao->where('id='.$data['id'])->save($data);

		}else{
			$have=$Dao->where("user_no = '".$data['user_no']."' and acc_id=".$data['acc_id'])->field('count(1) as num  ')->select();
			if($have[0]['num']){
				$result= 'NOunique';
			}else{
			    if(empty($data['shop_id']) || empty($data['acc_id'])){
			        $result= 'needParam';
			    }else{
			        $result=$Dao->add($data);
			    }

			}
		}
		echo $result;
	}
	public function mima(){
		$Dao=M('operator');
		$data['id'] = I('post.id', '', 'trim') ;
		$password=I('post.pwd') ;
		if($password&&$password!=''){
			$passwordinfo = password($password);
			$data['pwd'] = $passwordinfo['password'];
			$data['encrypt'] = $passwordinfo['encrypt'];
		}
		$result=$Dao->where('id='.$data['id'])->save($data);
		if($result){
			return "1";
		}else{
			return "0" ;
		}
	}
	/**
	 * 保存管理员
	 */
	public function saveManager(){

		$Dao=M('operator');
		$data['id'] = I('post.id', '', 'trim') ;
		$data['user_no'] = I('post.user_no', '', 'trim') ;
		$data['user_name'] = I('post.user_name', '', 'trim') ;
		$data['mobile'] = I('post.mobile', '', 'trim') ;
                $data['status'] = I('post.status', '1', 'trim') ;
                $data['category'] = I('post.category', '1', 'trim') ;
                $data['city_id'] = I('post.city_id', '1', 'trim') ;
		$data['is_ht'] =1;

		if(!empty($data['id'])){
                        $save['user_name'] = $data['user_name'];
                        $save['status'] = $data['status'];
                        $save['category'] = $data['category'];
                        $save['city_id'] = $data['city_id'];
                        $password=I('post.pwd') ;
		        $passwordinfo = password($password);
		        $save['pwd'] = $passwordinfo['password'];
		        $save['encrypt'] = $passwordinfo['encrypt'];
                        $result=$Dao->where('id='.$data['id'])->save($save);

                        if($result === false){
				ajax_output(1,'修改失败');
			}else{
				ajax_output(0,'修改成功');
			}
                        die;
		}else{
		    $have=$Dao->where(" user_no='".$data['user_no']."'")->find();
		    if($have){
		        $result= 'NOunique';
                        echo $result;
		        exit();
		    }

		    $password=I('post.pwd') ;
		    if($password && $password!=''){
		        $passwordinfo = password($password);
		        $data['pwd'] = $passwordinfo['password'];
		        $data['encrypt'] = $passwordinfo['encrypt'];
		    }

		    $data['create_time'] = Date('Y-m-d H:i:s')  ;
		   $result=$Dao->add($data);
//                   $ss=$Dao->getLastSql();
//                   file_put_contents("G:/a.txt",$ss,FILE_APPEND);
		}
		echo $result;
	}
	/**
	 * 删除员工
	 */
	public function removeMember(){

                $ids= I('post.ids', '', 'trim');//要删除的门店ID
                $table = M('');
                $table->startTrans();

                $stss = M('operator')->where(array('id' =>array("in",$ids)))->getField('sts');
                if($stss==0){
                    $sts=2;
                }else{
                    $sts=0;
                }
                $departure_time=date('Y-m-d H:i:s');
                $reles = M('operator')->where(array('id' =>array("in",$ids)))->save(array('sts' => $sts,'departure_time'=>$departure_time));
                if($reles===false){
                    $table->rollback();
                    ajax_output(1);
                }
                $table->commit();
                ajax_output(0);
	}
	/**
	 * 员工离职
	 */
	public function MemberLiZhi(){
		$Dao=M('operator');
		$data['id'] = I('post.id', '', 'trim') ;
		$data['sts'] = I('post.sts', '', 'trim') ;
		$data['leave_time'] = Date('Y-m-d H:i:s')  ;
		$result=$Dao->where('id='.$data['id'])->save($data);
		echo $result;
	}
	/**
	 * 启用员工
	 */
	public function UserMember(){
		$Dao=M('operator');
		$data['id'] = I('post.id', '', 'trim') ;
		$data['sts'] = I('post.sts', '', 'trim') ;
		$result=$Dao->where('id='.$data['id'])->save($data);
		echo $result;
	}

    /**
     * 修改个人信息
     */
    public function personalInfo(){
        $user_id = cookie('saas_admin_userid');
        $where['id'] = $user_id;

        if($_POST){
            $save['user_name'] = I('user_name','','trim');
            $pwd = I('pwd','','trim');
            $pwd_length = strlen($pwd);
            if(!($pwd_length>= 6 && $pwd_length <=18)) ajax_output('1','密码长度应为6-18位！');
            $old_pwd = I('old_pwd','','trim');
            if(empty($save['user_name'])||empty($pwd)||empty($old_pwd)){
                ajax_output('1','帐号，密码不能为空！');
            }
            $user_info = M('admin')->where($where)->find();
            $old_pwd = password($old_pwd,$user_info ['encrypt']);
            if($old_pwd !== $user_info['pwd']){
                ajax_output('1','原密码输入有误！');
            }
            $pwd = password($pwd);
            $save['pwd'] = $pwd['password'];
            $save['encrypt'] = $pwd['encrypt'];
            $res = M('admin')->where($where)->save($save);

            if($res !== false){
                echo ajax_output('0','保存信息成功');
            }else{
                echo ajax_output('1','该账户已存在');
            }die;
        }
        $info = M('admin')->where($where)->find();
        echo json_encode($info);
    }
}
