<?php
namespace Caryu\Model;

use Think\Model;

class ConfigModel extends Model{

    protected $tableName = 'config';
    protected $pk = 'id';

    /**
     *  获取设置的值
     */
    public function getValue($key){

        $this->where(['key'=>$key])->getField('value');
        return  json_decode($this->where(['key'=>$key])->getField('value'),true);
    }

}