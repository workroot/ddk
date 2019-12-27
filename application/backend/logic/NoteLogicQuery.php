<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use think\Exception;

class NoteLogicQuery extends LogicQuery{

    /**
     * 单条数据查询
     * @param null $params
     * @param string $select
     * @return mixed
     * @throws \Exception
     */
    public function findOne($params = null,$select="*"){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $data = db('note')->where('id','=',intval($params['id']))->field($select)->order('id','=',intval($params['id']))->find();
        }catch(\Exception $e ){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 添加文章
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $params["lasttime"] = time();
            $object->allowField(true)->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 文章列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params=null,$object){
        try{
            $map = [];
            if (is_numeric($params)) {
                $map['a.id'] = $params['keyword'];
            }else{
                $map['a.title'] = ['like', "%{$params['keyword']}%"];
            }
            //注册时间
            $data = $object
                ->alias("a")
                ->field("a.id,a.title,a.lasttime,a.descc,a.jianjie,b.tname")
                ->join("__NOTETYPE__ b","a.tid=b.id","LEFT")
                ->where($map)
                ->order('id desc')->paginate(15, false, ['query'=>request()->param()]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 更新数据
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function update($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $note           = $object->find($params['id']);
            $note->id       = $params['id'];
            $note->title = $params['title'];
            $note->content   = $params['content'];
            $note->lasttime =time();
            $note->types    = $params['types'];
            $note->jianjie    = $params['jianjie'];
            $note->tid    = $params['tid'];
            $note->thumb    = $params['thumb'];
            $note->save();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }
}