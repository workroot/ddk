<?php

namespace app\backend\logic;



use app\backend\base\LogicQuery;

class BannerLogicQuery extends LogicQuery {


    /**
     * 列表
     * @param null $params
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function indexList($params = null , $object){
         try{
             $map = [];
             if(is_numeric($params['keyword'])){
                 $map['a.id'] = intval($params['keyword']);
             }else{
                 $map['a.names'] = ['like', "%{$params['keyword']}%"];
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
     * 添加图片
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $params["createdAt"] = time();
            $object->allowField(true)->save($params);
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
    public function update($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $banner           = $object->find($params['id']);
            $banner->id       = $params['id'];
            $banner->names = $params['names'];
            $banner->hrefs   = $params['hrefs'];
            $banner->descc    = $params['descc'];
            $banner->thumb   = $params['thumb'];
            $banner->type   = $params['type'];
            $banner->updatedAt   = time();
            $banner->save();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }
}