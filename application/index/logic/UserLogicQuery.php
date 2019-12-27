<?php

namespace app\index\logic;

use app\common\base\LogicQuery;
use think\Db;

class UserLogicQuery extends LogicQuery
{

    /**
     * 客户列表信息
     * @param null $params
     * @return array
     * @throws \Exception
     */
    public function query($params = null)
    {
        try {
            if (empty($params)) {
                $params = $this->request->param();
            }
            $map = [];
            if (isset($params['keyword']) && !empty($params['keyword'])) {
                $map['a.names|a.mobile|a.idcard|a.id'] = ['like', "%{$params['keyword']}%"];
            }
            $map1 = [];

            if (isset($params['keyword']) && empty($params['keyword'])) {
                if (isset($params['date1']) && !empty($params['date1'])) {
                    $d1 = strtotime($params['date1']);
                    $map['a.create_time'] = [['>=', $d1], ['<=', time()]];
                }
                if (isset($params['date2']) && !empty($params['date2'])) {
                    $d2 = strtotime($params['date2']);
                    $map['a.create_time'] = [['>=', !empty($d1) ? $d1 : 0], ['<=', $d2]];
                }
                if (empty($params['date1']) && empty($params['date2'])) {
                    $map['a.create_time'] = [['>=', strtotime('-15 day', time())], ['<=', time()]];
                }
            }

            if (isset($params['pingfeng']) && $params['pingfeng'] == 1) {
                $order = 'a.pingfeng desc';
            } else {
                $order = 'a.id desc';
            }

            $map['a.agent_class']=array('=',1);

            $data = Db::name('user')
                ->alias("a")
                ->field("a.*,c.names pnames")
                ->where($map)
                ->join("__USER__ c",'a.pid = c.id','LEFT')
                ->order($order)->paginate(15, false, ['query'=>request()->param()]);

            $statistics['count'] = Db::name('user')->alias("a")->where($map)->count('a.id');
            $zao=strtotime(date('Y-m-d',time()));
            $wan=time();
            $mapjin['a.create_time'] = array(array('>=',$zao),array('<=',$wan));
            $mapyouxiao['a.pingfeng']=array('>','0');
            $statistics['countyouxiaojin'] = Db::name('user')->alias("a")->where($map)->where($mapjin)->where($mapyouxiao)->count('a.id');
            $statistics['countjin'] = Db::name('user')->alias("a")->where($map)->where($mapjin)->count('a.id');
            $statistics['countyouxiao'] = Db::name('user')->alias("a")->where($map)->where($mapyouxiao)->count('a.id');
            return ['data'=>$data,'statistics'=>$statistics];
        } catch (\Exception $e) {
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 客户信息导出
     * @param null $params
     * @throws \Exception
     */
    public function excel($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $map = [];
            if (isset($params['keyword']) && !empty($params['keyword'])) {
                $map['a.names|a.mobile|a.idcard|a.id'] = ['like', "%{$params['keyword']}%"];
            }
            $map1 = [];
            if (!isset($params['keyword']) && empty($params['keyword'])) {
                if (isset($params['date1']) && !empty($params['date1'])) {
                    $d1 = strtotime($params['date1']);
                    $map['a.create_time'] = [['>=', $d1], ['<=', time()]];
                }
                if (isset($params['date2']) && !empty($params['date2'])) {
                    $d2 = strtotime($params['date2']);
                    $map['a.create_time'] = [['>=', !empty($d1) ? $d1 : 0], ['<=', $d2]];
                }

                if (empty($params['date1']) && empty($params['date2'])) {
                    $map['a.create_time'] = [['>=', strtotime('-15 day', time())], ['<=', time()]];
                }
            }

            if (isset($params['pingfeng']) && $params['pingfeng'] == 1) {
                $order = 'a.pingfeng desc';
            } else {
                $order = 'a.id desc';
            }

            $map['a.agent_class']=array('=',1);

            $data = Db::name('user')
                ->alias("a")
                ->field("a.*,c.names pnames")
                ->where($map)
                ->join("__USER__ c",'a.pid = c.id','LEFT')
                ->order($order)->paginate(15, false, ['query'=>request()->param()]);

            vendor("PHPExcel.PHPExcel");
            vendor("PHPExcel.PHPExcel.Writer.Excel5");
            vendor("PHPExcel.PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.PHPExcel.IOFactory");
            $objPHPExcel=new \PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', '姓名')
                ->setCellValue('C1', '手机')
                ->setCellValue('D1', '创建时间')
                ->setCellValue('E1', '推荐人')
                ->setCellValue('F1', '有效');


            if ($data){
                $i=2;  //定义一个i变量，目的是在循环输出数据是控制行数
                $count = count($data);  //计算有多少条数据
                for ($i = 2; $i <= $count+1; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $data[$i-2]["id"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $data[$i-2]["names"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $data[$i-2]["mobile"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, date('Y-m-d H:i:s',$data[$i-2]["create_time"]));
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $data[$i-2]["pnames"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $data[$i-2]["pingfeng"]);

                }
            }
            $objPHPExcel->getActiveSheet()->setTitle('用户');      //设置sheet的名称
            $objPHPExcel->setActiveSheetIndex(0);                   //设置sheet的起始位置
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //Excel2003通过PHPExcel_IOFactory的写函数将上面数据写出来
            $PHPWriter = \PHPExcel_IOFactory::createWriter( $objPHPExcel,"Excel2007"); //Excel2007
            header('Content-Disposition: attachment;filename="用户.xlsx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }

}

