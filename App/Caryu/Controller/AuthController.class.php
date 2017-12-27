<?php
namespace Caryu\Controller;
use Caryu\Controller\CommonController;
/**
 * 门店管理模块
 * @author Vania
 */
class AuthController extends CommonController{
    //权限分组
    public function group(){
        //pid为0，meau为null顶级权限
//                $data = M('admin_auth_rule')->where(array('pid'=>0))->select();//顶级权限
        //pid为null，meau为0菜单
        $meau = M('admin_auth_rule')->where(array('meau'=>0))->select();//菜单

        foreach($meau as $k=>$v){
            $arr = M('admin_auth_rule')->where(array('meau'=>$v['id']))->select();
            $meau[$k]['childData'][] = $arr;
            $arr=$this->assoc_unique($arr, 'pid');
            foreach($arr as $ks=>$vs){
                $res=M('admin_auth_rule')->where(array('id'=>$vs['pid']))->select();
                //echo M('admin_auth_rule')->_sql();
                if(!empty($res)){
                    $meau[$k]['permission'][]=$res;
                }
                foreach ($res as $keys=>$values){
                    $id=$values['id'];
                    $result=M('admin_auth_rule')->where("pid='$id' and (meau is null or meau=0)")->select();
                    if(!empty($result)){
                        $meau[$k]['childData'][] = $result;

                    }
                }

                //var_dump($meau[$k]['childData']);

            }
        }

//                echo json_encode($meau);die;
        $this->assign('meau',$meau);
        $this->display();
    }


    public function assoc_unique($arr, $key) {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }

    public function getGroups() {
        $array = D('Auth')->getGroups();
        exit(json_encode($array));
    }
    public function save() {
        $id = I('post.id',0,'intval');
        $title = I('post.title','','trim');
        $data['title'] = $title;
        $res =  $this->getRules();
        $data['rules'] = $res;
        if(!$data['title']) $this->error('请填写分组名称');
        if(!$data['rules']) $this->error('请至少设置一个权限');
        if($id) $result = M('adminAuthGroup')->where(array('id'=>$id))->save($data);
        else $result = M('adminAuthGroup')->add($data);
        if($result !== false) $this->success('保存成功');
        else echo M('adminAuthGroup')->_sql();
    }
    public function remove() {
        D('Auth')->remove();
    }
    private function getRules() {
        $rules = I('post.rules',array());
        $res = implode(',', $rules);
        return $res;
    }

    //权限列表
    public function authList(){
        $meau = M('admin_auth_rule')->where(array('meau'=>0))->select();//菜单
        $pid = M('admin_auth_rule')->where(array('pid'=>0))->select();//权限
        $this->assign('meau',$meau);
        $this->assign('pid',$pid);
        $this->display();
    }
    //得到权限数据
    public function getAuthList(){
        $page = I('page','1','trim');
        $num= I('rows','20','trim');
        $data= M('admin_auth_rule')->limit($num)->page($page)->select();
        $count = M('admin_auth_rule')->count();
        $arr ['total'] = $data ? $count: 0;
        $arr ['rows'] = !empty($data) ? $data :array();
        exit(json_encode($arr,true));
    }

    //保存菜单数据
    public function saveAuth(){

        $add['name'] = I('post.name','','trim');
        $add['title'] = I('post.title','','trim');
        $type = I('post.type','','trim');

        if($type=='-1'){//菜单
            $add['meau'] =0;
            $add['pid']=NULL;
        }else if($type=='-2'){//权限
            $add['meau'] =NULL;
            $add['pid']=0;
        }else if($type=='-3'){ //子权限(不出现在菜单上的)
            $add['meau'] =NULL;
            $add['pid'] = I('post.pid','','trim');
        }else{          //子菜单
            $add['pid'] = I('post.pid','','trim');
            $add['meau'] = I('post.meau','','trim');
        }
        $result = M('admin_auth_rule')->add($add);
        if($result !==false){
            ajax_output(0,'保存成功');
        }else{
            ajax_output(1,'保存失败');
        }

    }
    //删除数据
    public function delAuth(){

        $result =M('admin_auth_rule')->where(array('id'=>I('post.id')))->delete();
        if($result)$this->success('删除成功');
        else $this->error('删除失败');

    }
}
