<?php

namespace app\backend\logic;



use app\backend\base\LogicQuery;
use think\Exception;

class AuthAgentLogicQuery extends LogicQuery {


    /**
     * 单条数据查询
     * @param null $parans
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function findOne($parans = null , $object){
        if(empty($params)){
            $params = $this->request->param();
        }
        try{
            if(!isset($params['id']) && empty($params['id'])){
                throw new Exception('参数错误',1);
            }
            $data = $object->where('id','=',$params['id'])->find();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return isset($data) && !empty($data) ? $data : '';
    }


    /**
     * 代理类型列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function agentList($params = null , $object){
        try{
            $map = [];
            if(is_numeric($params['keyword'])){
                $map['a.id'] = intval($params['keyword']);
            }else{
                $map ['b.agent_name'] = ['like', "%{$params['keyword']}%"];
            }
            $data = $object
                ->alias("a")
                ->field("a.*,b.agent_name,p.name")
                ->where($map)
                ->join("__AGENT__ b","a.aid = b.id",'LEFT')
                ->join("__PRODUCT__ p","a.pid = p.id ",'LEFT')
                ->order('a.id DESC')->paginate(15, false, ['query'=>request()->param()]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 插入数据
     * @param null $params
     * @param $object
     */
    public function save($params = null,$object){
        if(empty($params)){
            $params = $this->request->param();
        }
        try{
            if(!empty($params)){
                $params['createdAt'] = time();
                $object->allowField(true)->save($params);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 更新数据
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function updated($params = null,$object){
        if(empty($params)){
            $params = $this->request->param();
        }
        try{
            if(!empty($params)){
                $params['updatedAt'] = time();
                if ($object->allowField(true)->save($params,$params['id']) === false) {
                    throw new Exception('跟新失败',1);
                }
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }




    /**
     * 查询条件
     * @param $query
     * @param $params
     */
    public function params($query,$params){
        if (isset($params['id']) && is_numeric($params['id']) && !empty($params['id'])) {
            $query->where('c.id','=',intval($params['id']));
        }
        if(isset($params['comp']) && !empty($params['comp'])){
            $query->where('c.name','=',"{$params['comp']}");
        }
        if(isset($params['uid']) && !empty($params['uid'])){
            $query->where('c.uid','=',$params['uid']);
        }
        $query->where('c.isdel','=',1);
    }

}