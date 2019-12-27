<?php

namespace app\calculator\logic;

use app\calculator\base\LogicQuery;
use think\Db;

class ApayLogicQuery extends LogicQuery{


    /**
     * 文章列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params=null,$object){
        try{
            if(empty($params)){
                $params = $this->request->param();
            };
            $map = $this->query($params);
            //注册时间
            $data = $object
                ->alias("a")
                ->field("a.*,u.mobile,g.names")
                ->join("__USER__ u","a.uid = u.id","LEFT")
                ->join("__USER__ g","a.agentId = g.id","LEFT")
                ->where($map)
                ->order('id desc')->select();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 支付统计
     * @param null $params
     * @return array
     * @throws \Exception
     */
    public function count($params = null){
        try{
            $start_day = mktime(0,0,0,date('m'),date('d'),date('y'));
            $end_day = mktime(23,59,59,date('m'),date('d'),date('y'));
            $day = [];
            $day['pay'] = Db::name('LawyerOrder')->where('status','=',1)->where('createAt','between',[$start_day,$end_day])->count();
            $day['wpay'] = Db::name('LawyerOrder')->where('status','=',0)->where('createAt','between',[$start_day,$end_day])->count();
            $day['zzpay'] = Db::name('LawyerOrder')->where('status','=',1)->count();
            $day['zwpay'] = Db::name('LawyerOrder')->where('status','=',0)->count();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $day;
    }


    /**
     * 条件
     * @param $params
     * @return array
     */

    public function query($params){
        $map = [];
        if(isset($params['keyword']) && !empty($params['keyword'])){
            $map['u.mobile|g.names|a.id|a.out_trade_no'] = ['like', "%{$params['keyword']}%"];
        }
        
        if(isset($params['ctime']) && !empty($params['ctime'])){
            $data = explode(' - ',$params['ctime']);
            $map['a.createAt'] = ['between',[strtotime($data[0]),strtotime($data[1])]];
        }
        
        if(isset($params['status'])){
             $map['a.status'] = $params['status'];
        }
       
        
        return $map;
    }



}