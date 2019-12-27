<?php
namespace app\backend\logic;

use app\backend\base\LogicQuery;

class CityLogicQuery extends LogicQuery{


    /**
     * 城市列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params = null , $object){
         try{
             if (isset($params['id']) && is_numeric($params['id'])){
                 $where["pid"] = $params['id'];
                 $parent = $object->where(array('id'=>$params['id']))->find();
                 $this->assign("parent",$parent);
             }else{
                 $where["pid"] = 0;
                 $this->assign("parent",null);
             }
             $data = $object->where($where)->order("ID ASC")->select();
         }catch(\Exception $e){
             $this->log($e);
             throw $e;
         }
         return $data;
    }


    /**
     * 多条查询
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function findALl($params = null , $object){
        try{
            if (isset($params['pid']) && is_numeric($params['pid'])){
                $where["id"]= intval($params['pid']);
                $info = $object->where($where)->find();
                $data = $object->where(array('pid'=>$info["pid"]))->select();
            }else{
                $data = $object->where(array('pid'=>0))->select();
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 添加地址
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $object->allowField(true)->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }
}