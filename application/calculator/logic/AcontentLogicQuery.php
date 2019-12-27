<?php
namespace app\calculator\logic;

use app\calculator\base\LogicQuery;
use think\Exception;

class AcontentLogicQuery extends LogicQuery {


    /**
     * 添加律师问题
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null, $object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }

            $validate_result = $this->validate($params, 'Clawyer');
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
                ->alias("l")
                ->field("l.*")
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
                $map['l.id'] = $params['keyword'];
            }else{
                $map['l.title'] = ['like', "%{$params['keyword']}%"];
            }
        }

        if(isset($params['ctime']) && !empty($params['ctime'])){
            $data = explode(' - ',$params['ctime']);
            $map['l.createdAt'] = ['between',[strtotime($data[0]),strtotime($data[1])]];
        }
        return $map;
    }

}