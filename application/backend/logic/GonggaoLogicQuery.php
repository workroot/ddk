<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use think\Exception;

class GonggaoLogicQuery extends LogicQuery{



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
            $data = db('Gonggao')->where('id','=',intval($params['id']))->field($select)->order('id','=',intval($params['id']))->find();
        }catch(\Exception $e ){
            $this->log($e);
            throw $e;
        }
        return $data;
    }

    /**
     * 数据列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params=null , $object){
        try{
            $map = [];
            if (is_numeric($params['keyword'])) {
                $map['a.id'] = $params['keyword'];
            }else{
                $map['a.title|a.marks'] = ['like', "%{$params['keyword']}%"];
            }
            $data = $object
                ->alias("a")
                ->field("a.*")
                ->where($map)
                ->order('id DESC')->paginate(15, false, ['query'=>request()->param()]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 添加公告
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null , $object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $validate_result = $this->validate($params, 'Gonggao');
            if ($validate_result !== true) {
                throw new Exception($validate_result,1);
            }

            $params["createdAt"]=time();
            $object->allowField(true)->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }

    /**
     * 更新
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function update($params = null , $object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $validate_result = $this->validate($params, 'Gonggao');
            if ($validate_result !== true) {
                $this->error($validate_result);
            }
            $gonggao           = $object->find($params['id']);
            $gonggao->id       = $params['id'];
            $gonggao->title = $params['title'];
            $gonggao->marks   = $params['marks'];
            $gonggao->descc    = $params['descc'];
            $gonggao->type    = $params['type'];
            $gonggao->personalId    = $params['personalId'];
            $gonggao->updatedAt    = time();
            $gonggao->save();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }
}