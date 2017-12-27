<?php
namespace Xiaochengxu\Model;


use Think\Model;

class RedPacketModel extends Model
{
    private $config;
    private $red_packet_rocord;
    private $user;

    public function __construct()
    {
        $this->config = D('Caryu/config');
        $this->red_packet_rocord = M('red_packet_rocord');
        $this->user = M('user');
    }
    

    /**
     * 洗车红包
     */
    public function washCar($openId)
    {
        $res = $this->config->getValue('new_washer');

        //检测活动状态
        if ($res['on_off']) {

            //检测用户状态
            $isOld = $this->user->where(['openId' => $openId])->getField('NewWashCarRedPacket');
            if ($isOld) return return_output(1,'非新用户（驾遇快洗）');

            //发送红包
            $table = new Model();
            $table->startTrans();
            $count = count($res['redPacket']);
            $num = 0;
            //插入红包记录表
            foreach ($res['redPacket'] as $item) {

                $add['openId'] = $openId;
                $add['type'] = 'new_washer';
                $add['name'] = $item['name'];
                $add['money'] = $item['money'];
                if($item['pass_day']){
                    $add['start_time'] = date('Y-m-d',SYS_TIME + $item['pass_day'] * 3600 *24).' 00:00:00';
                }else{
                    $add['start_time'] = date('Y-m-d')." 00:00:00";
                }

                $add['over_time'] = date('Y-m-d',SYS_TIME + $item['days'] * 3600 *24).' 23:59:59';
                $add['add_time'] = SYS_DATE;
                $add['remark'] = $res['remark'];

                $this->red_packet_rocord->add($add);
                $num++;
            }
            $res = $this->user->where(['openId' => $openId])->save(['NewWashCarRedPacket' => 1]);
            if ($num == $count && $res) {
                $table->commit();
                return return_output(0,'红包已成功下发');
            } else {
                $table->rollback();
                return return_output(1,'红包下发失败');
            }
        } else {
            return return_output(1,'活动未开启，无法领取活动礼包');
        }
    }

    /**
     * 获取某人的洗车红包(可用的红包)
     */
    public function washCarRedPacket($openid){

        $redPacket = $this->red_packet_rocord->where(['openId'=>$openid,'status'=>0])->order('over_time DESC')->select();

        $data = [];
        if($redPacket){
            $data = multisortArray($redPacket,'money',SORT_DESC);
            foreach ($data as $key => $value){
                $data[$key]['start_time'] = date('Y-m-d',strtotime($value['start_time']));
                $data[$key]['over_time'] = date('Y-m-d',strtotime($value['over_time']));

                $start =  $data[$key]['start_time'] <= date('Y-m-d');
                $end =  $data[$key]['over_time'] >= date('Y-m-d');

                if($start && $end){
                    $data[$key]['can_use'] = 1;     //开始
                }else{
                    if(!$start){
                        $data[$key]['can_use'] = -1;     //还没开始
                    }
                    if(!$end){
                        $data[$key]['can_use'] = -2;    //已经过期
                    }
                }
            }
        }
        return  $data;
    }


    /**
     * 获取某人的红包
     */
    public function getRedPacket($openid,$packeid){
        $info =  $this->red_packet_rocord->where(['openid'=>$openid,'id'=>$packeid])->find();
        if($info){
            $info['start_time'] = date('Y-m-d',strtotime($info['start_time']));
            $info['over_time'] = date('Y-m-d',strtotime($info['over_time']));

            $start =  $info['start_time'] <= date('Y-m-d');
            $end =  $info['over_time'] >= date('Y-m-d');

            if($start && $end && $info['status'] == 0){
                $info['can_use'] = 1;     //开始
            }else{
                $info['can_use'] = 0;
            }
        }
        return $info;
    }


}