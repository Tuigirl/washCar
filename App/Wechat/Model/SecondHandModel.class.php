<?php
namespace Wechat\Model;

use Think\Model;

class SecondHandModel extends Model
{
    /**
     * 生成订单
     */
    public function sellingCar($data, $carPrice,$open_id,$isQuotes=0)
    {
        if (!empty($carPrice)) {
            $max = array($carPrice['max_sell_price'], $carPrice['max_buy_price'], $carPrice['max_private_price']);
            $min = array($carPrice['min_sell_price'], $carPrice['min_buy_price'], $carPrice['min_private_price']);
            rsort($max);//零售价取最大的
            sort($min);//收购价取最小
            //以平台最大价格为最终报价
            $vehicle_price = sprintf('%.2f', $max[0] / 10000);//零售价
            //价格走势
            $price_in_half_year = $carPrice['price_in_half_year'];
            $maxs = array($price_in_half_year['sell'], $price_in_half_year['buy'], $price_in_half_year['private']);
            $mins = array($price_in_half_year['sell'], $price_in_half_year['buy'], $price_in_half_year['private']);
            rsort($maxs);
            sort($mins);
            //收购价当月不能小于下月的价格，取满足条件的最小值
            foreach ($min as $k => $v) {
                if ($v > $mins[0][0][1]) {
                    $purchasings[] = $v;
                }
            }
            sort($purchasings);
            $purchasing_price = sprintf('%.2f', $purchasings[0] / 10000);//收购价
            $priceTrend['vehicle_price'] = $this->Conversion($maxs[0]);
            $priceTrend['purchasing_price'] = $this->Conversion($mins[0]);
            $priceTrend['vehicle_price'] = $this->arrayMearge($priceTrend['vehicle_price'], $vehicle_price);
            $priceTrend['purchasing_price'] = $this->arrayMearge($priceTrend['purchasing_price'], $purchasing_price);
        } else {
            $vehicle_price = '';
            $purchasing_price = '';
            $priceTrend = (object)array('vehicle_price'=>array(),'purchasing_price'=>array());
        }

        $row = array();
        $row['name'] = is_empty($data['name']);
        $row['mobile'] = is_empty($data['mobile']);
        $row['city'] = is_empty($data['city']);
        $row['city_id'] = is_empty($data['city_id']);
        $row['open_id'] = $open_id;
        $row['detail_model_id'] = is_empty($data['detail_model_id']);
        $row['card_time'] = is_empty($data['card_time']);
        $row['mileage'] = is_empty($data['mileage']);
        $row['vehicle_price'] = $vehicle_price;
        $row['purchasing_price'] = $purchasing_price;
        $row['status'] = 1;
        $row['update_time'] = SYS_DATE;
        $row['add_time'] = SYS_DATE;
        $row['isQuotes']=$isQuotes;
        $row['order_id'] = getOrderId();
        $id = M('car_secondhand')->add($row);

        if ($id) {
            $carModels = M('car_library')->where(array('detail_model_id' => $data['detail_model_id']))->find();
            $result['model_name'] = is_empty($carModels['model_name']);
            $result['detail_model_name'] = is_empty($carModels['detail_model_name']);
            $result['brand_logo'] = is_empty($carModels['brand_logo']);
            $result['card_time'] = !empty($data['card_time']) ? date('Y年m月', strtotime($data['card_time'])) :'';
            $result['mileage'] = is_empty($data['mileage']);
            $result['vehicle_price'] = $vehicle_price;
            $result['purchasing_price'] = $purchasing_price;
            $result['priceTrend'] = $priceTrend;

            //将订单发送给驾遇
            A('Wechat/CarInfo')->is_return = 1;
            $res = A('Wechat/CarInfo')->sendSecondHandToCaryu($row);
            $res = json_decode($res,true);
            if($res['code']){
                ajax_output(1, $res['msg']);
            }else{
                ajax_output(0, '', $result);
            }
        } else {
            ajax_output(1, '提交失败');
        }
    }

    /**
     * 数据转化，将年去掉
     */
    public function Conversion($date)
    {
        foreach ($date as $k => $v) {

            $v[1] = sprintf('%.2f', $v[1] / 10000);
            $result[] = $v;
        }
        return $result;
    }

    /**
     * 数组合并排序
     */
    public function arrayMearge($data, $price)
    {
        $data = array_merge($data, array(array(date('Y-m', time()), $price)));

        $minPrice = sprintf('%.2f', $data[5][1] * 0.97);
        $data = array_merge($data, array(array(date('Y-m', strtotime("+7month")), $minPrice)));
        $data = $this->multisortArray($data, 'month', SORT_DESC);
        foreach ($data as $k => $v) {
            $month = explode('-', $v[0]);
            $v['month'] = $month[1];
            $v['price'] = $v[1];
            unset($v[0]);
            unset($v[1]);
            $result[] = $v;
        }

        return $result;
    }

    /**
     * 二维数组按某个元素来排序
     * SORT_DESC
     */
    public function multisortArray($arr, $need, $sort = SORT_ASC)
    {
        $flag = array();
        if ($arr) {
            foreach ($arr as $k => $arr2) {
                $flag[$k] = $arr2[$need];
            }
            array_multisort($flag, $sort, $arr);
        }
        return $arr;
    }

    /**
     *  获取订单的详细信息
     */
    public function orderInfo($orderId,$shopId){
        return M('car_secondhand')->where(['order_id'=>$orderId,'shop_id'=>$shopId])->field('order_id,detail_model_id,city,card_time,mileage,vehicle_price,isQuotes,status,add_time,source')->find();
    }

    /**
     *  获取订单更改时间
     */
    public function orderChangTime($orderId,$currentStatus)
    {
        $data = [];
        $model = M('car_secondhand_status_log');

        //提交时间
        if ($currentStatus >= 1) {
            $data['submitTime'] = M('car_secondhand')->where(['order_id' => $orderId])->getField('add_time');
            $data['submitTime'] = date("Y-m-d H:i:s");
        }

        if ($currentStatus >= 2) $data['handleTime'] = $model->where(['order_id' => $orderId, 'to' => 2])->getField('add_time'); //处理时间
        if ($currentStatus == 3) $data['successTime'] = $model->where(['order_id' => $orderId, 'to' => 3])->getField('add_time'); //成功时间
        if ($currentStatus == 4) $data['failTime'] = $model->where(['order_id' => $orderId, 'to' => 4])->getField('add_time'); //失败时间

        //每次返回都是四个值（写在这里方便维护）
        if(empty($data['submitTime'])) $data['submitTime'] = '';
        if(empty($data['handleTime'])) $data['handleTime'] = '';
        if(empty($data['successTime'])) $data['successTime'] = '';
        if(empty($data['failTime'])) $data['failTime'] = '';

        return $data;
    }

}
