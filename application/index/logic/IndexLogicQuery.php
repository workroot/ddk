<?php

namespace app\index\logic;


use app\api\logic\LoginLogicQuery;
use app\backend\logic\AgentLogicQuery;
use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use app\common\model\Fault;
use think\Db;
use think\Exception;

class IndexLogicQuery extends LogicQuery{


    /**
     * 单条数据查询
     * @param null $params
     * @param string $select
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function findOne($params = null,$select="c.*"){
        try{
            if(empty($params)){
                throw new \Exception('参数错误',1);
            }
            $query = Db::name('Company');
            $query->alias('c');
            $query->field($select);
            $this->params($query,$params);
            $data = $query->find();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 第一步条件判断:指定参数
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function condition($params = null){
        try{
            //代理ID
            $agentid = isset($params['uid']) && !empty($params['uid']) ? $params['uid'] : Session('user_id');
            //判断是否为代理连接进来的
            if(empty($params['pid'])){
                if(isset($agentid) && !empty($agentid)){
                    $price = Db('AgentPrice')->field('id,price')->where('uid','=',$agentid)->where('product_type','=',Mapper::PRODUCT_TYPE)->where('isdel','=',1)->order('createdAt desc')->find();
                }else{
                    $price = Db('AgentPrice')->field('id,price')->where('id','=',Mapper::PID_PINGTTAI_ID)->where('product_type','=',Mapper::PRODUCT_TYPE)->where('isdel','=',1)->find();
                }
            }
            if(!isset($params['mobile']) && empty($params['mobile'])){
                throw new \Exception('手机号码不能为空',1);
            }
            //用户ID
            $uid = LoginLogicQuery::getInstance()->ordinarySave(['mobile'=>$params['mobile'],'password'=>'m123456','uid'=>$agentid]);
            $data = $this->findOne($params,'c.id,c.name,c.logo');
            if(isset($data) && !empty($data)){
                Db::name('Company')->where('id',$data['id'])->setInc('flow');
                $data['cid'] = $data['id'];
                $data['pid'] = isset($params['pid']) && !empty($params['pid']) ? $params['pid'] : $price['id'];
                $data['uid'] = $uid;
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }

    /**
     * 第二次判断
     * @param null $params
     * @throws \Exception
     */
    public function condition_paid($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            if(!isset($params['pid']) && empty($params['pid'])){
                throw new Exception('版本信息有误,请联系代理商重新更换版本连接',1);
            }

            $price = AgentPriceLogicQuery::getInstance()->condition(['id'=>$params['pid']]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $price;
    }



    /**
     * 查询条件
     * @param $query
     * @param $params
     */
    public function params($query,$params){
        if (isset($params['id']) && !empty($params['id'])) {
            $query->where('c.id','=',intval($params['id']));
        }
        if(isset($params['comp']) && !empty($params['comp'])){
            $query->where('c.name','=',"{$params['comp']}");
        }
        $query->where('c.isdel','=',1);
    }


    /**
     * 过失数据记录
     * @param null $params
     * @throws \Exception
     */
    public function fault($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $data = [
                'mobile' => $params['mobile'],
                'title' => $params['comp'],
                'createdAt' => time()
            ];
            $model = new Fault();
            $model->allowField(true)->save($data);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }




}