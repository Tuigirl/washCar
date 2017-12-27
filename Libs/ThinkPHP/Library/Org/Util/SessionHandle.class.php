<?php
/**
* session写入数据库
*/
namespace Org\Util;
class SessionHandle {

	 protected $lifeTime=604800 ,$dao;


     function __construct(&$params='') {
		$this->lifeTime = C('EXPIRE_TIME') ?  C('EXPIRE_TIME') : 24*3600*30;
		$this->dao = M('LoginSession');
    }

     function open($savePath, $sessName) {
       return true;
    }

    function close() {
	   return $this->gc($this->lifetime);
   }

    function read($id,$type) {
	   $r = $this->dao->where(array('id'=>$id,'type'=>$type))->find();
	   return $r ? $r : '';
   }

    function write($userid,$sessID,$sessData,$type) {

		$sessiondata = array(
                'id'=>$userid,
				'sessionid'=>$sessID,
				'session_data'=>$sessData,
				'session_expires'=>time()+$this->lifeTime,//保存一个月
				'adddotime'=>time(),
				'type'=>$type,
		);
		 $result=$this->dao->add($sessiondata);
		 return $result;

   }
   function save ($session_id){
   		$result=$this->dao->where(array('sessionid'=>$session_id))->save(array('session_expires'=>time()+$this->lifeTime));
   		return $result;
   }


    function destroy($sessID) {
	   $arr = $this->dao->delete($sessID);
	   return $arr;
   }


    /**
     * 根据sessioniD获取对应登录的会员ID
     * @param  [type] $session_id
     * @param  [type] $type
     * @param  [type] $isHtml true 以html形式返回
     * @return [type]  $type      [description]
     */
     function getsession_userid($session_id,$type=0,$isHtml=false){

        if(empty($session_id)){
          if(!$isHtml){
                 $data['code']=2;
                 $data['msg']='请先登录';
                 echo json_encode($data);die();
           }else{
						 echo '<div id="loginInvalid"></div>';
           }
        }
        $s_arr=$this->dao->where(array('sessionid'=>$session_id))->field('id,session_expires,type')->find();
        $member_id=$s_arr['id'];
         if(empty($member_id) || time() > $s_arr['session_expires']){
             if(!$isHtml){
                 $data['code']=2;
                 $data['msg']='请先登录';
                 echo json_encode($data);die();
             }else{
							 echo '<div id="loginInvalid"></div>';
             }

        }
        $this->save($session_id);
       	if($type ==0 ){
       		return (int)$member_id;
       	}else{
			return $s_arr;
       	}

    }

}

?>
