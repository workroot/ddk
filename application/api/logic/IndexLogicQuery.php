<?php

namespace app\api\logic;


use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use think\Db;
use think\Exception;

class IndexLogicQuery extends LogicQuery{


    /**
     * 查询
     * @param null $params
     * @param string $select
     * @return \think\Paginator
     * @throws \Exception
     */
    public function findAll($params = null,$select="c.*"){
        try{
            $query = Db::name('Company');
            $query->alias('c');
            $query->field($select);
            $this->params($query,$params);
            $data = $query->order('id DESC')->paginate(50, false, ['query'=>request()->param()]);
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
        if(isset($params['company']) && !empty($params['company'])){
            $query->where('c.name','like',"%{$params['company']}%");
        }
        if (isset($params['id']) && is_numeric($params['id']) && !empty($params['id'])) {
            $query->where('c.id','=',intval($params['id']));
        }

        if(isset($params['keyword']) && !is_numeric($params['keyword']) && !empty($params['keyword'])){
            $query->where('c.name|c.mechanism|c.uname','like',"%{$params['keyword']}%");
        }
        if(isset($params['start_time']) && !empty($params['start_time'])){
            $startime = strtotime($params['start_time']);
            $query->where('c.createdAt','>=',$startime);
        }
        if(isset($params['end_time']) && !empty($params['end_time'])){
            $endtime = strtotime($params['end_time']);
            $query->where('c.createdAt','<=',$endtime);
        }
        if(isset($params['uid']) && !empty($params['uid'])){
            $query->where('c.uid','=',$params['uid']);
        }

        $query->where('c.isdel','=',1);
    }

    /**
     * 第一步条件判断:指定参数
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function condition($params = null){
        try{
            if(!isset($params['pid']) && empty($params['pid'])){
                $price = Db('AgentPrice')->field('id,price')->where('id','=',1)->where('product_type','=',Mapper::PRODUCT_TYPE)->where('isdel','=',1)->find();
            }
            $data = $this->findOne($params,'c.id,c.name,c.logo');
            $data['cid'] = PublicMethod::encryption($data['id']);
            $data['pid'] = isset($params['pid']) && !empty($params['pid']) ? $params['pid'] : PublicMethod::encryption($price['id']);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 数据转换
     * @param null $params
     * @param string $select
     * @return string
     * @throws \Exception
     */
    public function  indexs($params = null,$select="c.*"){
        $data = $this->findAll($params,$select);
        if(empty($params['pid'])){
            $price = Db('AgentPrice')->field('id,price')->where('id','=',1)->where('product_type','=',Mapper::PRODUCT_TYPE)->where('isdel','=',1)->find();
        }
        $pid = isset($params['pid']) && !empty($params['pid']) ? $params['pid'] : PublicMethod::encryption($price['id']);
        $index = "";
        foreach($data as $value){
            $cid = PublicMethod::encryption($value['id']);
            $index .= '<li class="mui-table-view-cell"><a href="/index/index/paid?cid='.$cid.'&cname='.$value['name'] .'&pid='.$pid.'&logo='.$value['logo'].'" class="mui-navigate-right">'. $value['name'] .'</a></li>';
        }
        return $index;
    }


    public function index($params = null , $select = "c.*"){
        $data = $this->findAll($params,$select);
        $index = "";
        foreach($data as $value){
            $cid = PublicMethod::encryption($value['id']);
            $index .= '<li class="mui-table-view-cell del" data-cid="'. $cid .'" data-name="'. $value['name'] .'" data-logo="'. $value['logo'] .'"><a href="javascript:void(0);"class="mui-navigate-right">'. $value['name'] .'</a></li>';
        }
        return $index;
    }


}