<?php
namespace Caryu\Controller;

class WashDeviceController extends CommonController
{

    protected $model;
    protected $wechat;

    public function index()
    {
        $this->display();
    }

    public function __construct()
    {
        parent::__construct();
        $this->model = D('WashDevice');
        $this->wechat = A('Wechat/WeChat');
    }


    /**
     *  洗车设备列表
     */

    public function washDeviceList()
    {

        $device = I('device', '', 'trim');
        $city = I('city', '', 'trim');
        $timeStart = I('time_start', '', 'trim');
        $timeEnd = I('time_end', '', 'trim');
        $rows = I('rows', '', 'trim');
        $page = I('page', '', 'trim');

        if ($device) $where['device'] = $device;
        if ($city) $where['city'] = $city;

        if ($timeStart && empty($timeEnd)) $where['add_time'] = array('gt', $timeStart);
        if ($timeEnd && empty($timeStart)) $where['add_time'] = array('lt', $timeEnd . " 23:59:59");
        if ($timeEnd && $timeStart) $where['add_time'] = array('between', array($timeStart, $timeEnd . " 23:59:59"));
        if ($device) $where['washCar_device'] = $device;
        if ($city) $where['city'] = $city;

        $data['list'] = $this->model->where($where)->limit($rows)->page($page)->select();
        $data['total'] = $this->model->where($where)->count();
        echo json_encode($data);
    }

    /**
     * 添加设备
     */

    public function addDevice()
    {
        $param = array('washDevice', 'address', 'city');
        $check = array();
        $data = $this->checkParam($param, $check);

        $has = $this->model->where(['washCar_device' => $data['washDevice']])->find();
        if ($has) {
            ajax_output(1, '该设备' . $data['washDevice'] . '已经存在！');
        }

        $add['washCar_device'] = $data['washDevice'];
        $add['address'] = $data['address'];
        $add['add_time'] = $add['update_time'] = SYS_DATE;
        $add['qrcode'] = trim($add['qrcode'], '.');
        $add['city'] = $data['city'];
        $this->model->add($add);

        //生成二维码
        $save['qrcode'] = $this->wechat->getwxacode($data['washDevice']);
        $this->model->where(['washCar_device' => $data['washDevice']])->save($save);
        ajax_output(0, 'success');
    }

    /**
     * 修改设备
     */

    public function updateDevice()
    {
        $param = array('washDevice', 'address', 'city');
        $check = array();
        $data = $this->checkParam($param, $check);

        $has = $this->model->where(['washCar_device' => $data['washDevice']])->find();

        if ($has) {
            $add['washCar_device'] = $data['washDevice'];
            $add['address'] = $data['address'];
            $add['add_time'] = $add['update_time'] = SYS_DATE;
            $add['qrcode'] = trim($add['qrcode'], '.');
            $add['city'] = $data['city'];
            $this->model->where(['washCar_device' => $data['washDevice']])->save($add);

            //生成二维码
            $save['qrcode'] = $this->wechat->getwxacode($data['washDevice']);
            $this->model->where(['washCar_device' => $data['washDevice']])->save($save);
            ajax_output(0, 'success');
        } else {
            ajax_output(1, '该设备不存在！');
        }
    }
}