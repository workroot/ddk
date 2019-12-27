<?php

namespace app\calculator\logic;


use app\calculator\base\LogicQuery;

class AcommentLogicQuery extends LogicQuery{


    /**
     * 文章列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params=null,$object){
        try{
            if(empty($params)){
                $params = $this->request->param();
            };
            $map = $this->query($params);
            //注册时间
            $data = $object
                ->alias("c")
                ->field("c.*")
                ->where($map)
                ->order('id desc')->select();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }



    public function query($params){
        $map = [];
        if(isset($params['keyword']) && !empty($params['keyword'])){
            if (is_numeric($params['keyword'])) {
                $map['c.id'] = $params['keyword'];
            }else{
                $map['c.title'] = ['like', "%{$params['keyword']}%"];
            }
        }
        if(isset($params['ctime']) && !empty($params['ctime'])){
            $data = explode(' - ',$params['ctime']);
            $map['c.createdAt'] = ['between',[strtotime($data[0]),strtotime($data[1])]];
        }
        $map['c.type'] = 2;
        return $map;
    }



}