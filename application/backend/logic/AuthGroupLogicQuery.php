<?php

namespace app\backend\logic;


use app\backend\base\LogicQuery;
use think\Exception;

class AuthGroupLogicQuery extends LogicQuery {

    /**
     * 添加权限组
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result =  $this->validate($params, 'AuthGroup');
            if ($result !== true) {
                throw new Exception($result,1);
            }
            $object->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 权限组更新
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function update($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            if ($params['id'] == 1 && $params['status'] != 1) {
                throw new Exception('超级管理组不可禁用',1);
            }
            $result =  $this->validate($params, 'AuthGroup');
            if ($result !== true) {
                throw new Exception($result,1);
            }
            if ($object->save($params,$params['id']) === false) {
                throw new Exception('跟新失败',1);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


}