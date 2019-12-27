<?php

namespace app\index\logic;

use app\common\base\LogicQuery;
use app\common\model\User;
use think\Db;
use think\Exception;

class PersonalLogicQuery extends LogicQuery{


    /**
     * 用户信息查询
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function getUser($params = null){
            try{
                if(empty($params)){
                    $params = $this->request->param();
                }
                if(isset($params['type']) && $params['type'] == 2){
                    $user = Db::name('user')->alias('u')->field('u.mobile,u.names,u.money,u.total_achievement,u.gender,u.province,u.city,u.create_time,u.pid,a.mobile as mobiles')->join('__USER__ a','a.id = u.pid')->where('u.id',session('uid'))->find();
                }else{
                    $user = Db::name('user')->alias('u')->field('u.mobile,u.names,u.money,u.total_achievement,u.gender,u.province,u.city,u.create_time,u.pid,a.mobile as mobiles')->join('__USER__ a','a.id = u.pid')->where('u.id',session('user_id'))->find();
                }
                return $user;
            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }
    }


    /**
     * 用户数据更新
     * @param null $params
     * @throws \Exception
     */
    public function edit($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            if(isset($params['names']) && !empty($params['names'])){
                if(!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u',$params['names'])){
                    throw new Exception('格式不对,用户名只能为中英文',1);
                }
            }
            $uid = session('user_id');
            db('user')->where('id','=',$uid)->update($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


}