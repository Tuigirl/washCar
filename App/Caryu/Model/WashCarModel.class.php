<?php
namespace Caryu\Model;

use Think\Model;

require_once THINK_PATH . 'Library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
define('G_HTTP_HOST', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
define('G_HTTP', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
define("WEB_PATH", dirname(G_HTTP . G_HTTP_HOST . $_SERVER['SCRIPT_NAME']));

class WashCarModel extends Model{

    protected $tableName = 'wash_order';
    protected $pk = 'id';
    protected $error ='';


    /**
     * 洗车的excel
     */
    public function WashCarExcel($data){

        set_time_limit(0);
        ini_set("memory_limit", "-1");

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '订单号')
            ->setCellValue('C1', '设备名称')
            ->setCellValue('D1', '收入金额(元)')
            ->setCellValue('E1', '收入时间');

        $num = 1;
        foreach ($data['list'] as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($key + 2), ' ' . $num);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . ($key + 2), ' ' . $value['order_id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . ($key + 2), ' ' . $value['device']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . ($key + 2), ' ' . $value['pay_money']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . ($key + 2), ' ' . $value['pay_time']);
            $num++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('共享洗车缴费');
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        $name = iconv('utf-8', 'utf-8', '共享洗车缴费' . date("Y-m-d"));
        $filenames = './Public/upload/Excel/' . $name . '.xls';
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filenames);
        return $filenames;
    }


    /**
     * 问题反馈列表的excel
     */
    public function feedBackExcel($data){

        set_time_limit(0);
        ini_set("memory_limit", "-1");

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '设备名称')
            ->setCellValue('C1', '所在城市')
            ->setCellValue('D1', '提交时间')
            ->setCellValue('E1', '故障原因');

        $num = 1;
        foreach ($data['list'] as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($key + 2), ' ' . $num);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . ($key + 2), ' ' . $value['device']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . ($key + 2), ' ' . M('city')->where(['id'=>$value['city']])->getField('name'));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . ($key + 2), ' ' . $value['add_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . ($key + 2), ' ' . $value['feedBack']);
            $num++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('共享洗车---问题反馈');
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        $name = iconv('utf-8', 'utf-8', '共享洗车---问题反馈' . date("Y-m-d"));
        $filenames = './Public/upload/Excel/' . $name . '.xls';
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filenames);
        return $filenames;
    }

}