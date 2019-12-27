<?php

namespace app\index\logic;

use app\common\base\LogicQuery;
use think\Db;
use think\Exception;

class NoteLogicQuery extends LogicQuery{

    protected $resultSetType = 'collection';
    /**
     * 新闻列表
     * @param null $params
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \Exception
     */
    public function query($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            //var_dump($params);die;
            if(!isset($params['tid']) && empty($params['tid'])){
                $params['tid'] = 2;
            }
            $query = Db::name('note');
            $this->querys($query,['tid'=>intval($params['tid']),'page'=>isset($params['page'])?$params['page']:1,'limit'=>isset($params['limit'])?$params['limit']:10]);
            $data = $query->select();
            if(!empty($data)){
                $this->supplement($data);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 数据填充
     * @param $data
     */
    public function supplement(&$data){
        foreach($data as &$item){
            $item['lasttime'] = date('Y-m-d H:i:s',$item['lasttime']);
        }
    }


    /**
     * 查询条件
     * @param $query
     * @param $params
     */
    public function querys($query,$params){
        if(isset($params['tid']) && !empty($params['tid'])){
            $query->where('tid','=',$params['tid']);
        }
        if(isset($params['page']) && !empty($params['page'])){
            $limit = isset($params['limit'])?$params['limit']:10;
            $query->page($params['page'],$limit);
        }
        $query->order('descc desc,lasttime desc');
    }


    /**
     * 详情
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function detail($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }

            if(!isset($parms['id']) && empty($params['id'])){
                throw new Exception('参数错误');
            }

            $data = Db::name('note')->where('id','=',intval($params['id']))->find();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


}