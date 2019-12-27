<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use think\Exception;

class NotetypeLogicQuery extends LogicQuery{


    /**
     * 文章分类列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params = null,$object){
        try{
            $map = [];
            if (isset($params['keyword']) && is_numeric($params['keyword'])) {
                $map['a.id'] = $params['keyword'];
            }else{
                $map['a.tname'] = ['like', "%{$params['keyword']}%"];
            }
            $notetype_list = $object->alias("a")->field("a.*")->where($map)->order('id DESC')->paginate(15, false, ['query'=>request()->param()]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $notetype_list;
    }


    /**
     * 添加类目录
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null , $object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $params['createdAt'] = time();
            $object->allowField(true)->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 更新类目录
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function update($params=null , $object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $notetype           = $object->find($params['id']);
            $notetype->id       = $params['id'];
            $notetype->tname = $params['tname'];
            $notetype->descc    = $params['descc'];
            $notetype->thumb   = $params['thumb'];
            $notetype->updatedAt = time();
            $notetype->save();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


}