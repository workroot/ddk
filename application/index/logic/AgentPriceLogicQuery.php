<?php

namespace app\index\logic;

use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use think\Db;
use think\Exception;

class AgentPriceLogicQuery extends LogicQuery{


    /**
     * 单条数据查询
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function findOne($params=null,$select="p.*"){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $query = Db('AgentPrice');
            $query->alias('p');
            $query->field($select);
            $this->params($query,$params);
            $data = $query->find();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 判断产品金额信息
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function condition($params = null){
        try{
            $params['pd_type'] = true;
            $price = Db('AgentPrice')->where('id','=',intval($params['id']))->where('isdel','=',1)->find();
            if(!isset($price) || empty($price)){
                throw new Exception('版本信息有误,请联系代理商重新更换版本连接',1);
            }

            //授权代理数据
            $authAgent =  Db('AuthAgent')->where('id','=',$price['a_p_id'])->find();

            if(!isset($authAgent) && empty($authAgent)){
                throw new Exception('授权代理信息错误,请联系代理商重新更换',1);
            }

            //代理数据
            $agent =  Db('Agent')->where('id','=',$authAgent['aid'])->find();
            if(!isset($agent) && empty($agent)){
                throw new Exception('授权代理信息错误,请联系代理商重新更换',1);
            }

            //版本信息
            $proecut =  Db('Product')->where('id','=',$authAgent['pid'])->find();
            if(!isset($proecut) && empty($proecut)){
                throw new Exception('授权代理信息错误,请联系代理商重新更换',1);
            }

            //判断版本价格是否大于设置的最高价格
            if($price['price'] > $authAgent['highestprice']){
                throw new Exception('支付金额已超出平台规定价格,请联系代理商重新更换',1);
            }

            //判断代理设置的价格是否低于平台版本价，测试账号除外
            if($price['price'] < $authAgent['price'] && !in_array($price['uid'],Mapper::$TEST_ID)){
                throw new Exception('支付金额已低于平台成本价格,请联系代理商重新更换',1);
            }
            $price['is_share'] = $agent['is_share'];
        }catch(\Exception $e){
             $this->log($e);
             throw $e;
        }
        return  $price ;
    }


    /**
     * 查询条件
     * @param $query
     * @param $params
     */
    public function params($query,$params){
        if(isset($params['pd_type']) && $params['pd_type']){
            if(isset($params['id']) && !empty($params['id'])){
                $query->where('p.id','=',intval($params['id']));
            }else{
                throw new Exception('参数错误',1);
            }
        }
        if(isset($params['id']) && !empty($params['id'])){
            $query->where('p.id','=',intval($params['id']));
        }
        if(isset($params['uid']) && !empty($params['uid'])){
                $query->where('p.uid','=',$params['uid']);
        }

        if(isset($params['product_type']) && !empty($params['product_type'])){
            $query->where('p.product_type','=',$params['product_type']);
        }

        if(isset($params['a_p_id']) && !empty($params['a_p_id'])){
            $query->where('p.a_p_id','=',$params['a_p_id']);
        }
        $query->where('p.isdel','=',1);
    }


}