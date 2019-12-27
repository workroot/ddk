<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use app\common\model\Fault;
use helper\Rsas;
use think\Db;

class InquireLogicQuery extends LogicQuery{

    protected $resultSetType = 'collection';
    /**
     * 查询客户列表
     * @param null $params
     * @return array
     * @throws \Exception
     */
    public function query($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $map = [];
            $map1 = [];
            if(isset($params['keyword']) && !empty($params['keyword'])){
                $map['o.cname|u.mobile|o.p_name|o.cname'] = ['like',"%{$params['keyword']}%"];
            }
            if(!empty($params['date1'])){
                $d1=strtotime($params['date1']);
                $map['o.createdAt'] = [['>=',$d1],['<=',time()]];
                $map1['o.createdAt'] = [['>=',$d1],['<=',time()]];
            }

            if(!empty($params['date2'])){
                $d2=strtotime($params['date2']);
                $map['o.createdAt'] = [['>=',!empty($d1)?$d1:0],['<=',$d2]];
                $map1['o.createdAt'] = [['>=',!empty($d1)?$d1:0],['<=',$d2]];
            }
            if(empty($params['date1']) && empty($params['date2'])){
                $aid = Session('admin_id');
                if($aid == 13){

                }else{
                    $map['o.createdAt'] = [['>=',strtotime('-15 day',time())],['<=',time()]];
                    $map1['o.createdAt'] = [['>=',strtotime('-15 day',time())],['<=',time()]];
                }
            }

            $data = Db::name('order')
                ->alias('o')
                ->field('o.*,u.mobile,a.names as aname,a.mobile as amobile,a.id as aid,t.names as tname , t.id as tid')
                ->join('__USER__ u','o.uid = u.id','LEFT')
                ->join('__USER__ a','o.proxyid = a.id','LEFT')
                ->join('__USER__ t','u.pid = t.id','LEFT')
                ->where($map)->where('o.status','=',1)->order(['o.createdAt'=>'DESC'])->paginate(15, false, ['query'=>request()->param()]);
            $statistics['count'] = Db::name('order')->alias("o")->where($map1)->where('o.status','=',1)->count('o.id');
            $statistics['sum'] = Db::name('order')->alias("o")->where($map1)->where('o.status','=',1)->sum('o.price');
            $zao=strtotime(date('Y-m-d',time()));
            $wan=time();
            $mapjin['o.createdAt'] = array(array('>=',$zao),array('<=',$wan));
            $statistics['countjin'] = Db::name('order')->alias("o")->where($map1)->where('o.status','=',1)->where($mapjin)->count('o.id');
            $statistics['sumjin']= Db::name('order')->alias("o")->where($map1)->where('o.status','=',1)->where($mapjin)->sum('o.price');

            return ['data'=>$data,'statistics'=>$statistics];
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }

    }


    /**
     * 数据转换
     * @param $data
     */
    public function fillQueryList(&$data){
        foreach($data as &$item){
            $item['signs'] = Rsas::getInstance()->encode(http_build_query(['id'=>$item['cid'],'out_trade_no'=>$item['number_order']]));
            $item['createdAt'] = date('Y-m-d H:i:s',$item['createdAt']);
        }
    }


    /**
     * 查询客户信息导出
     * @param null $params
     * @throws \Exception
     */
    public function excel($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $map = [];
            if(isset($params['keyword']) && !empty($params['keyword'])){
                $map['o.cname|u.mobile|o.p_name|o.cname'] = ['like',"%{$params['keyword']}%"];
            }
            if(!empty($params['date1'])){
                $d1=strtotime($params['date1']);
                $map['o.createdAt'] = [['>=',$d1],['<=',time()]];
            }

            if(!empty($params['date2'])){
                $d2=strtotime($params['date2']);
                $map['o.createdAt'] = [['>=',!empty($d1)?$d1:0],['<=',$d2]];
            }
            if(empty($params['date1']) && empty($params['date2'])){
                $aid = Session('admin_id');
                if($aid == 13){

                }else{
                    $map['o.createdAt'] = [['>=',strtotime('-15 day',time())],['<=',time()]];
                }
            }
            $data = Db::name('order')
                ->alias('o')
                ->field('o.*,u.mobile,a.names as aname,a.mobile as amobile,a.id as aid,t.names as tname , t.id as tid')
                ->join('__USER__ u','o.uid = u.id','LEFT')
                ->join('__USER__ a','o.proxyid = a.id','LEFT')
                ->join('__USER__ t','u.pid = t.id','LEFT')
                ->where($map)->where('o.status','=',1)->order(['o.createdAt'=>'DESC'])->paginate(15, false, ['query'=>request()->param()]);
            vendor("PHPExcel.PHPExcel");
            vendor("PHPExcel.PHPExcel.Writer.Excel5");
            vendor("PHPExcel.PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.PHPExcel.IOFactory");
            $objPHPExcel=new \PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', '查询人手机')
                ->setCellValue('C1', '查询公司')
                ->setCellValue('D1', '渠道')
                ->setCellValue('E1', '价格')
                ->setCellValue('F1', '查询时间');
            if ($data){
                $i=2;  //定义一个i变量，目的是在循环输出数据是控制行数
                $count = count($data);  //计算有多少条数据
                for ($i = 2; $i <= $count+1; $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $data[$i-2]["id"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $data[$i-2]["mobile"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $data[$i-2]["cname"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $data[$i-2]["aname"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $data[$i-2]["price"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, date('Y-m-d H:i:s',$data[$i-2]["createdAt"]));
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