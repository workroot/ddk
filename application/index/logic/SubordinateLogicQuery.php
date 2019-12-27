<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use think\Db;

class SubordinateLogicQuery extends LogicQuery{


    /**
     * 分页
     * @param null $params
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \Exception
     */
    public function querys($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $userid = session('user_id');
            $query = Db::name('user');
            $this->query($query,['userid'=>$userid,'page'=>isset($params['page'])?$params['page']:1,'limit'=>isset($params['limit'])?$params['limit']:10]);
            $data = $query->select();
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
    public function query($query,$params){
        if(isset($params['userid']) && !empty($params['userid'])){
            $query->where('pid','=',$params['userid']);
        }
        if(isset($params['page']) && !empty($params['page'])){
            $limit = isset($params['limit'])?$params['limit']:10;
            $query->page($params['page'],$limit);
        }
        $query->order('create_time desc');
    }


}