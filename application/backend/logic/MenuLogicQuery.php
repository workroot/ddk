<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use think\Exception;

class MenuLogicQuery extends LogicQuery {


    /**
     * 添加菜单
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result =  $this->validate($params, 'Menu');
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
     * 菜单更新
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function update($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result =  $this->validate($params, 'Menu');
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