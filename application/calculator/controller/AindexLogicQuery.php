<?php

namespace app\calculator\logic;

use app\calculator\base\LogicQuery;
use think\Db;

class AindexLogicQuery extends LogicQuery{


    public function count($params = null){
        try{
            if(empty($params)) {
                $params = $this->request->param();
            }

            $start_day = mktime(0,0,0,date('m'),date('d'),date('y'));
            $end_day = mktime(23,59,59,date('m'),date('d'),date('y'));
            $day = Db::name('Clawyer')->field('sum(awesome) as awesome , sum(responses) as responses , sum(browse) as browse')->where('createdAt','between',[$start_day,$end_day])->find();
            $day['registered'] = Db::name('LawyerOrder')->where('status','=',0)->where('createAt','between',[$start_day,$end_day])->count();
            $day['type'] = 1;

            $week = Db::name('clawyer')->field('sum(awesome) as awesome , sum(responses) as responses , sum(browse) as browse')->where('createdAt','between',[strtotime('- 7 day',time()),time()])->find();
            $week['registered'] = Db::name('LawyerOrder')->where('status','=',0)->where('createAt','between',[strtotime('- 7 day',time()),time()])->count();
            $week['type'] = 2;

            $month = Db::name('clawyer')->field('sum(awesome) as awesome , sum(responses) as responses , sum(browse) as browse')->where('createdAt','between',[strtotime('- 29 day',time()),time()])->find();
            $month['registered'] = Db::name('LawyerOrder')->where('status','=',0)->where('createAt','between',[strtotime('- 29 day',time()),time()])->count();
            $month['type'] = 3;

            $sum = Db::name('clawyer')->field('sum(awesome) as awesome , sum(responses) as responses , sum(browse) as browse')->find();
            $sum['registered'] = Db::name('LawyerOrder')->where('status','=',0)->count();
            $sum['type'] = 4;

            $daytime = 15;
            $data = [];
            $sums = 0;
            $sums_tim = 0;
            for($i = 0; $i <= $daytime ; $i++){
                $data[$i]['time'] = date('y-m-d',strtotime('-' . ($i).' day',time()));
                $data[$i]['times'] = date('m.d',strtotime('-' . ($i).' day',time()));
                $startTime = mktime(0,0,0,date('m',strtotime($data[$i]['time'])),date('d',strtotime($data[$i]['time'])),date('y',strtotime($data[$i]['time'])));
                $endTimesd = mktime(23,59,59,date('m',strtotime($data[$i]['time'])),date('d',strtotime($data[$i]['time'])),date('y',strtotime($data[$i]['time'])));
                $msp['createAt'] = array(array('>=',$startTime),array('<=',$endTimesd));
                $single = Db::name('LawyerOrder')->where('status','=',1)->where($msp)->count('id');
                $data[$i]['single1'] = !empty($single) && $single > 150 ? 150 : $single;
                $data[$i]['single2'] = !empty($single) && $single > 150 ? $single - 150 : 0;
                $data[$i]['single3'] = !empty($single) && $single > 300 ? $single - 300 : 0;
                $sums += $single;
                $countjin = Db::name('LawyerOrder')->where('status','=',0)->where($msp)->count('id');
                $sums_tim += $countjin;
                $data[$i]['countjin1'] = !empty($countjin) && $countjin > 150 ? 150 : $countjin;
                $data[$i]['countjin2'] = !empty($countjin) && $countjin > 150 ? $countjin - 150 : 0;
                $data[$i]['countjin3'] = !empty($countjin) && $countjin > 300 ? $countjin - 300 : 0;

            }
            $table = [];
            $table['time'] = $this->array_column($data,'times');
            $table['single1'] = $this->array_column($data,'single1');
            $table['single2'] = $this->array_column($data,'single2');
            $table['single3'] = $this->array_column($data,'single3');
            $table['countjin1'] = $this->array_column($data,'countjin1');
            $table['countjin2'] = $this->array_column($data,'countjin2');
            $table['countjin3'] = $this->array_column($data,'countjin3');
            $result = ['statistics'=>['day'=>$day,'week'=>$week,'month'=>$month,'sum'=>$sum],'data'=>$table,'sums'=>$sums,'sums_tim'=>$sums_tim];
        }catch(\Exception $e){
             $this->log($e);
             throw $e;
        }
        return $result;
    }



    public function array_column($params,$name=''){
        $data = [];
        foreach($params as $key => $value){
            $data[] = $value[$name];
        }
       /* $str = '';
        foreach($params as $key => $value){
            $str .= ','.$value[$name];
        }
        return substr($str,1);*/
       return $data;
    }

}