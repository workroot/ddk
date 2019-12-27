<?php

namespace app\api\logic;

use app\api\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use app\common\model\Lawyer;
use app\index\logic\AgentPriceLogicQuery;
use think\Exception;

class LawyerLogicQuery extends LogicQuery{

    /**
     * 生成用户数据
     * @param null $params
     * @throws \Exception
     */
    public function review($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $result = $this->validate($params , 'Lawyer');
            if($result !== true){
                throw new Exception($result,1);
            }
            //用户ID
            $uid = LoginLogicQuery::getInstance()->ordinarySave(['mobile'=>$params['mobile'],'password'=>'123456']);
            if(empty($uid)){
                throw new Exception('信息有误',1);
            }
            if(empty($params['pid'])){
                throw new Exception('信息有误',1);
            }
            $price = AgentPriceLogicQuery::getInstance()->condition(['id'=>$params['pid']]);
            $lid = $this->save(['content'=>$params['content'],'agentId'=>$price['uid'],'pid'=>$price['id'],'mobile'=>$params['mobile']]);
            $data['pid'] = PublicMethod::encryption($price['id']);
            $data['lid'] = PublicMethod::encryption($lid);
            $data['price'] = $price['price'];
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 律师执行插入
     * @throws \Exception
     */
    public function save($params){
        try{
            if(empty($params)){
                throw new Exception('参数不能为空',1);
            }
            $params['uid'] = Session('uid');
            $params['isPay'] = 0;
            $params['content'] = htmlspecialchars($params['content']);
            $params['createdAt'] = time();
            $lid = db('Lawyer')->insertGetId($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $lid;
    }
}