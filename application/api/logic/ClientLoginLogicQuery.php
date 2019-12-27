<?php

namespace app\api\logic;


use app\common\base\LogicQuery;
use app\common\base\Mapper;
use think\Db;
use think\Exception;
use think\Session;

class ClientLoginLogicQuery extends LogicQuery{

    /**
     * 用户登录
     * @param null $params
     * @throws \Exception
     */
    public function login($params = null){
        try{
            if(empty($params) || !isset($params['mobile']) && empty($params['mobile'])){
                throw new Exception('参数错误',1);
            }
            $where['mobile'] = $params['mobile'];
            $user = Db::name('user')->field('id,mobile,names,status,password,agent_class')->where($where)->where('agent_class','=',1)->find();
            if(isset($user) && !empty($user)){
                    $code = $this->captcha($params['mobile']);
                    if(!isset($code) && empty($code) || isset($code) && $code['code'] != $params['verification']){
                        throw new Exception('验证码错误',1);
                    }else{
                        Db::name('Captcha')->where('id','=',$code['id'])->update(['status' => '0','updatedAt'=>time()]);
                    }
                    if($user['status'] != 1){
                        throw new Exception('账号异常',1);
                    }
                session('umobile',$user['mobile']);
                session('ushare',isset($user['share']) && !empty($user['share']) ? $user['share'] : 0);
                session('uname',$user['names']);
                session('uid',$user['id']);
            }else{
                    $code = $this->captcha($params['mobile']);
                    if(!isset($code) && empty($code) || isset($code) && $code['code'] != $params['verification']){
                        throw new Exception('验证码错误',1);
                    }else{
                        Db::name('Captcha')->where('id','=',$code['id'])->update(['status' => '0','updatedAt'=>time()]);
                    }
                    if(empty($user)){
                        $data = [
                            'mobile' => $params['mobile'],
                            'password'         => 'm123456',
                            'password_confirm' => 'm123456',
                        ];
                        $uid = LoginLogicQuery::getInstance()->ordinarySave($data);
                    }
                session('umobile',$data['mobile']);
                session('ushare',Mapper::SHARE_USER);
                session('uname','');
                session('uid',$uid);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 验证码验证
     * @param $mobile
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function captcha($mobile){
        try{
            if(empty($mobile)){
                throw new Exception('手机号码错误',1);
            }
            Db::name('Captcha')->where('endtime','<=',time())->update(['status' => '0','updatedAt'=>time()]);
            $code = Db::name('Captcha')->where('mobile','=',$mobile)->where('status','=',1)->where('endtime','>=',time())->find();
            //清理所有已超时的验证码
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $code;

    }


    /**
     * 短信验证码发送
     * @param null $params
     * @throws \Exception
     */
    public function sms($params = null){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            SmsLogic::getInstance()->sms($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }



}