<?php

namespace app\backend\logic;


use app\backend\base\LogicQuery;
use helper\ModelHelperQuery;
use helper\StringHelper;
use think\Config;
use think\Exception;

class UserAgentLogicQuery extends LogicQuery{
    /**
     * 单条数据查询
     * @param null $parans
     * @param $object
     * @return mixed
     * @throws \Exception
     */
    public function findOne($params = null , $object){
        if(empty($params)){
            $params = $this->request->param();
        }
        try{
            if(isset($params['id']) && !empty($params['id'])){
                $map = ['id'=>$params['id']];
            }
            if(isset($params['mobile']) && !empty($params['mobile'])){
                $map = ['mobile'=>$params['mobile']];
            }
            $data = $object->where($map)->find();
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

            if(isset($params['keyword']) && !empty($params['keyword'])){
                $map['a.id|a.names|a.mobile|a.idcard'] = ['like', "%{$params['keyword']}%"];
            }

            if(isset($params['total_achievement'])){
                if($params['total_achievement'] > 0){
                    $map['a.total_achievement'] = ['>',0];
                }else{
                    $map['a.total_achievement'] = ['<=',0];
                }
            }

            if(!empty($params['uid'])){
                $map['a.id'] = ['=',$params['uid']];
            }

            if(isset($params['startTime']) && !empty($params['startTime'])){
                $startTime = strtotime($params['startTime']);
                $map['a.create_time'] = [['>=',!empty($startTime)?$startTime:0],['<=',time()]];
            }

            if(isset($params['endTime']) && !empty($params['endTime'])){
                $endTime = strtotime($params['endTime']);
                $map['a.create_time'] = [['>=',!empty($startTime)?$startTime:0],['<=',$endTime]];
            }

            $map['a.agent_class'] = ['>',1];
            $user_list = $object
                ->alias("a")
                ->field("a.*,b.agent_name,c.names pnames")
                ->where($map)
                ->join("__AGENT__ b","a.agent_class = b.id ",'LEFT')
                ->join("__USER__ c",'a.pid = c.id','LEFT')
                ->order('id DESC')->paginate(15, false, ['query'=>request()->param()]);

            $count = $object->alias("a")->where($map)->count('a.id');

            $zao = strtotime(date('Y-m-d',time()));
            $wan = time();
            $mapjin['a.create_time'] = array(array('>=',$zao),array('<=',$wan));
            $mapyouxiao['a.total_achievement']=array('>','0');

            $countyouxiaojin = $object->alias("a")->where($map)->where($mapjin)->where($mapyouxiao)->count('a.id');

            $countjin = $object->alias("a")->where($map)->where($mapjin)->count('a.id');

            $countyouxiao = $object->alias("a")->where($map)->where($mapyouxiao)->count('a.id');

        $data = ['user_list'=>$user_list ,'count'=>$count,'countyouxiaojin'=>$countyouxiaojin,'countjin'=>$countjin,'countyouxiao'=>$countyouxiao];

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
            $user = $this->findOne(['mobile'=>$params['mobile']],$object);

            if(isset($user) && !empty($user['agent_class']) && $user['agent_class'] > 1){
                throw new Exception('该手机号已被注册');
            }

            $params['password'] = md5($params['password'] . Config::get('salt'));
            $params['mid'] = ModelHelperQuery::countData('user',true,'DDK');
            $params['create_time'] = time();
            $params['pid'] = isset($params['pid']) && !empty($params['pid']) ? $params['pid'] : 1;

            if(isset($user) && !empty($user['agent_class']) && $user['agent_class']  <= 1){
                    $params['id'] = $user['id'];
                if ($object->allowField(true)->save($params,$params['id']) === false){
                    throw new Exception('跟新失败',1);
                }
            }else{
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
            if(!isset($params['id']) && empty($params['id'])){
                throw new Exception('参数错误',1);
            }
            $user = $this->findOne(['id'=>$params['id']],$object);
            if(isset($user) && !empty($user)){
                if(isset($user['mobile']) && $user['mobile'] != $params['mobile']){
                    $mobile = $this->findOne(['mobile'=>$params['mobile']],$object);
                    if(!empty($mobile)){
                        throw new \Exception('该手机号已注册',1);
                    }
                }
                if (!empty($params['password'])) {
                    $params['password'] = md5($params['password']. Config::get('salt'));
                }
                $params['updatedAt'] = time();
                if ($object->allowField(true)->save($params,$params['id']) === false){
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
            $query->where('p.id','=',intval($params['id']));
        }
        if(isset($params['comp']) && !empty($params['comp'])){
            $query->where('p.name','=',"{$params['comp']}");
        }
        if(isset($params['uid']) && !empty($params['uid'])){
            $query->where('p.uid','=',$params['uid']);
        }
    }
}