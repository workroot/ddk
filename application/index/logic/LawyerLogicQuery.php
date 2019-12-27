<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use think\Db;
use think\Exception;

class LawyerLogicQuery extends LogicQuery{

    /**
     * 第一步条件判断:指定参数
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function condition($params = null){
        try{
            //代理ID
            $agentid = Session('user_id');
            if(!isset($params['pid']) && empty($params['pid'])){
                if(isset($agentid) && !empty($agentid)){
                    $price = Db('AgentPrice')->field('id,price')->where('uid','=',$agentid)->where('product_type','=',Mapper::PRODUCT_TYPE_TWO)->where('isdel','=',1)->order('createdAt desc')->find();
                }else{
                    $price = Db('AgentPrice')->field('id,price')->where('id','=',Mapper::PID_ZIXUN_ID)->where('product_type','=',Mapper::PRODUCT_TYPE_TWO)->where('isdel','=',1)->find();
                }
            }
            $data['pid'] = isset($params['pid']) && !empty($params['pid']) ? $params['pid'] : PublicMethod::encryption($price['id']);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }



    /**
     * 单条数据查询
     * @param null $params
     * @param string $select
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function findOne($params = null,$select="l.*"){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $query = Db::name('Lawyer');
            $query->alias('l');
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
     * 查询条件
     * @param $query
     * @param $params
     */
    public function params($query,$params){
        if (isset($params['id']) && !empty($params['id'])) {
            $id = PublicMethod::decrypt($params['id']);
            $query->where('l.id','=',$id);
        }
        if(isset($params['uid']) && !empty($params['uid'])){
            $query->where('l.uid','=',$params['uid']);
        }
    }
}