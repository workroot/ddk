<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use app\common\base\Mapper;
use PHPExcel;
use PHPExcel_IOFactory;
use think\Db;

class ExcelLogicQuery extends LogicQuery
{


    protected function _initialize()
    {
        parent::_initialize();
        vendor("PHPExcel");
        vendor("PHPExcel.PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.IOFactory");
        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.Writer.Excel5");
        vendor("PHPExcel.PHPExcel.Writer.Excel2007");
        vendor("PHPExcel.PHPExcel.IOFactory");
        vendor("PHPExcel.PHPExcel.PHPExcel_Cell");

    }

    /**
     * 下载导入模板
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function excel()
    {
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $PHPExcel = new PHPExcel(); //实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("公司信息"); //给当前活动sheet设置名称
        //表头信息
        $PHPSheet->setCellValue("A1", "平台名称")
            ->setCellValue("B1", "逾期记录是否上征信")
            ->setCellValue('C1', '放贷前是否查征信')
            ->setCellValue('D1', '征信上体现名称')
            ->setCellValue('E1', '年龄要求')
            ->setCellValue('F1', '可贷额度')
            ->setCellValue('G1', '可贷期数')
            ->setCellValue('H1', '利息区间')
            ->setCellValue('I1', '网络小贷牌照')
            ->setCellValue('J1', '所属机构')
            ->setCellValue('K1', '公司地址')
            ->setCellValue('L1', '成立时间')
            ->setCellValue('M1', '公司所在地')
            ->setCellValue('N1', '联系电话')
            ->setCellValue('O1', '综合评分')
            ->setCellValue('P1', '电话催收')
            ->setCellValue('Q1', '司法起诉')
            ->setCellValue('R1', '上门催收');

        $PHPSheet->setCellValue("A2", "易贷宝")
            ->setCellValue("B2", "否")
            ->setCellValue('C2', '否')
            ->setCellValue('D2', '深圳前海微众银行股份有限公司')
            ->setCellValue('E2', '18-50')
            ->setCellValue('F2', '5百-5千')
            ->setCellValue('G2', '7-28天')
            ->setCellValue('H2', '年化36%以上')
            ->setCellValue('I2', '无')
            ->setCellValue('J2', '瀚宇天成金融信息服务（攸县）有限责任公司')
            ->setCellValue('K2', '湖南省株洲市攸县联星街道永佳社区佳园组攸州互联网金融创新中心1216室')
            ->setCellValue('L2', '3年以内')
            ->setCellValue('M2', '湖南')
            ->setCellValue('N2', '0731-8329-8178')
            ->setCellValue('O2', '1.6')
            ->setCellValue('P2', '5')
            ->setCellValue('Q2', '1')
            ->setCellValue('R2', '1');

        // 图片生成
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setPath('./public/upload/image/adminArticle/20190716/ce8d4abd33c565a93a4015f6a304ea8b.png');//这里拼接 . 是因为要在根目录下获取
        // 设置宽度高度
        $objDrawing->setHeight(80);//照片高度
        $objDrawing->setWidth(80); //照片宽度
        /*设置图片要插入的单元格*/
        $objDrawing->setCoordinates('Q2')->setWidth(65);
        // 图片偏移距离
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(3);
        $objDrawing->setWorksheet($PHPExcel->getActiveSheet());

        //设置表格行高
        $PHPExcel->getActiveSheet(0)->getRowDimension(2)->setRowHeight(20);
        //$PHPExcel->getActiveSheet(0)->getDefaultColumnDimension(2)->setWidth(15);
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header('Content-Disposition: attachment;filename="公司信息模板.xlsx"'); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output");
    }


    /**
     * 获取上传的数据
     * @param $info
     * @throws \Exception
     */
    public function inserExcel($info)
    {
        ignore_user_abort(true);  // 后台运行，不受前端断开连接影响
        set_time_limit(1800);
        try {
            //获取表单上传文件
            $file_name = ROOT_PATH . $info; //上传文件的地址
            //上传验证后缀名,以及上传之后移动的地址
            if (is_file($file_name)) {
                $extend = pathinfo($file_name);
                $extend = strtolower($extend["extension"]);
                if ($extend == 'xls') {
                    $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                } else if ($extend == 'xlsx') {
                    $objReader = new \PHPExcel_Reader_Excel2007();
                }
                $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array = $obj_PHPExcel->getActiveSheet();  //转换为数组格式
                $result = $this->datafile($excel_array); //数据处理
                if ($result) {
                    unlink($file_name);
                }
            }
        } catch (\Exception $e) {
            $this->log($e);
            throw $e;
        }

    }


    /**
     * 数据处理
     * @param $object
     * @return bool
     * @throws \Exception
     */
    public function datafile($object)
    {
        try {
            $result = $object->toArray();
            array_shift($result);
            $data = [];
            foreach ($result as $key => $value) {
                $company = Db::name('Company')->field('id,name')->where('name', '=', $value[0])->find();
                if (!isset($value[0]) && empty($value[0]) || isset($value[5]) && empty($value[5])) {
                    continue;
                }
                $data[$key]['id'] = isset($company['id']) && !empty($company['id']) ? $company['id'] : 0;
                $data[$key]['name'] = isset($value[0])?trim($value[0]):'';
                $data[$key]['isonletter'] = isset($value[1])?trim($value[1]):'';
                $data[$key]['isletter'] = isset($value[2])?trim($value[2]):'';
                $data[$key]['creditname'] = isset($value[3])?trim($value[3]):'';
                $data[$key]['age'] = isset($value[4])?trim($value[4]):'';
                $data[$key]['amount'] = isset($value[5])?trim($value[5]):'';
                $data[$key]['periods'] =  isset($value[6])?trim($value[6]):'';
                $data[$key]['interest'] = isset($value[7])?trim($value[7]):'';
                $data[$key]['islicense'] = isset($value[8])?trim($value[8]):'';
                $data[$key]['mechanism'] = isset($value[9])?trim($value[9]):'';
                $data[$key]['address'] = isset($value[10])?trim($value[10]):'';
                $data[$key]['established'] = isset($value[11])?trim($value[11]):'';
                $data[$key]['city'] = isset($value[12])?trim($value[12]):'';
                $data[$key]['phone'] = isset($value[13])?trim($value[13]):'';
                $data[$key]['score'] = isset($value[14])?trim($value[14]):'';
                $data[$key]['p_collection'] = isset($value[15])?trim($value[15]):'';
                $data[$key]['c_collection'] = isset($value[16])?trim($value[16]):'';
                $data[$key]['s_collection'] = isset($value[17])?trim($value[17]):'';
                $data[$key]['uname'] = Session('admin_name');
                $data[$key]['uid'] = Session('admin_id');
                $data[$key]['createdAt'] = time();
                $data[$key]['isdel'] = 1;
                $data[$key]['status'] = 2;
                $data[$key]['logo'] = '';
            }

            if (isset($data) && !empty($data)) {
                $data = $this->excelImg($object->getDrawingCollection(), $data);
            }
            //插入数据库
            $status = CompanyLogicQuery::getInstance()->saveAll($data);
            if ($status) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 图片剥离
     * @param $image
     * @param $data
     * @return mixed
     * @throws \PHPExcel_Exception
     */
    public function excelImg($image, $data)
    {
        foreach ($image as $key => $img) {
            //获取图片在excel里面的位置
            list ($startColumn, $startRow) = \PHPExcel_Cell::coordinateFromString($img->getCoordinates());
            $imgnames = $img->getPath();  //excel里面的图片路径
            $imageName = $img->getIndexedFilename();//图片名称
            //返回图片保存的路径
            $file = $this->images($imgnames, $imageName);
            if ($startRow < 2) {
                $data[$startRow]['logo'] = $file;
            } else {
                $data[$startRow - 2]['logo'] = $file;
            }


        }
        return $data;
    }


    /**
     * 处理excel里面的图片并保存返回路径
     * @param $filename
     * @param $file
     * @return bool
     */
    public function images($filename, $file)
    {
        $string = @file_get_contents($filename);
        $fileExt = substr($file, strripos($file, '.') + 1);
        $fileExt = strtolower($fileExt);
        $newFileName = md5(uniqid()) . '.' . $fileExt;
        $array = date('Ymd');
        $root = sprintf('%s' . IMAGES, realpath(ROOT_PATH));
        $dir = $root . '/company' . '/' . $array;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $openedfile = fopen($dir . '/' . $newFileName, "w");
        fwrite($openedfile, $string);
        fclose($openedfile);
        if ($string === FALSE) {
            $status = false;
        } else {
            $status = IMAGES . str_replace($root, '', $dir . '/' . $newFileName);
        }
        return $status;
    }

}