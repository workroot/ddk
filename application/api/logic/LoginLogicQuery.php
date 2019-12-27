<?php
namespace app\api\logic;

use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use app\common\model\Captcha;
use app\common\model\User;
use app\index\logic\AgentPriceLogicQuery;
use helper\ModelHelperQuery;
use helper\StringHelper;
use think\Config;
use think\Db;
use think\Exception;
use think\Session;

class LoginLogicQuery extends LogicQuery {


    /**
     * 保存
     * @param null $params
     * @return false|int
     * @throws \Exception
     */
      public function save($params = null){
            try{
                if(empty($params)){
                    throw new Exception('参数错误',1);
                }
                $result = $this->validate($params,'login');
                if($result !==  true ){
                    throw  new Exception($result,1);
                }
                if(isset($params['pid']) && !empty($params['pid'])){
                    $params['pid'] = PublicMethod::decrypt($params['pid']);
                    $agent_c = Db('user')->field('id,agent_class')->where('id','=',$params['pid'])->find();
                }
                $params['password'] = md5($params['password'] . Config::get('salt'));
                $params['mid'] = ModelHelperQuery::countData('user',true,'DDK');
                $params['status'] = 1;
                $params['create_time'] = time();
                $params['agent_class'] = isset($agent_c['agent_class']) && !empty($agent_c['agent_class']) ? $agent_c['agent_class'] : 2;
                $params['pid'] = isset($params['pid']) && !empty($params['pid']) ? $params['pid'] : 501492;
                
                if(!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u',$params['username'])){
                	throw new Exception('格式不对,用户名只能为中英文',1);
                }
                $params['names'] = isset($params['username']) && !empty($params['username']) ? $params['username'] : substr($params['mobile'],-4);
                $user = new User();
                $user->allowField(true)->save($params);
               // $userId = db('User')->insertGetId($params);
                Session::set('user_id',$user->id);
            	Session::set('user_mobile', $params['mobile']);
            	Session::set('user_name', $params['names']);
            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }
     }


    /**
     * 普通用户保存
     * @param null $params
     * @return false|int
     * @throws \Exception
     */
    public function ordinarySave($params = null){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $user = Db('User')->where('mobile','=',$params['mobile'])->where('agent_class','=',1)->find();
            if(isset($user) && !empty($user)){
                session('umobile',$user['mobile']);
                session('uname',$user['names']);
                session('ushare',$user['share']);
                session('uid',$user['id']);
                $user_id = $user['id'];
            }else{
                $data['mobile'] = $params['mobile'];
                $data['password'] = md5($params['password']);
                $data['mid'] = ModelHelperQuery::countData('user',true,'DDK');
                $data['status'] = 1;
                $data['create_time'] = time();
                $data['agent_class'] = 1;
                $data['share'] = Mapper::SHARE_USER;
                $data['pid'] = isset($params['uid']) && !empty($params['uid']) ? $params['uid'] : 501492;
                $data['names'] = substr($params['mobile'],-4);
                $user_id = db('User')->insertGetId($data);
                session('umobile',$data['mobile']);
                session('ushare',$data['share']);
                session('uname',$data['names']);
                session('uid',$user_id);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return isset($user_id) && !empty($user_id) ? $user_id : '';
    }


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
             $where['mobile'] = trim($params['mobile']);
             $user = Db::name('user')->field('id,mobile,names,status,password,agent_class')->where($where)->where('agent_class','<>',1)->find();
             if(isset($user) && !empty($user)){
                 if(isset($params['type']) && $params['type'] == 1){
                     if(empty($user)){
                         throw new Exception('手机号或密码错误',1);
                     }
                     $password = md5($params['password'] . Config::get('salt'));
                     if($user['password'] != $password){
                         throw new Exception('手机号或密码错误',1);
                     }
                 }else{
                     $code = $this->captcha($params['mobile']);
                     if(!isset($code) && empty($code) || isset($code) && $code['code'] != $params['verification']){
                         throw new Exception('验证码错误',1);
                     }else{
                         Db::name('Captcha')->where('id','=',$code['id'])->update(['status' => '0','updatedAt'=>time()]);
                     }
                 }
                 if($user['status'] != 1){
                     throw new Exception('账号异常',1);
                 }
             }else{
             	 if(isset($params['type']) && $params['type'] == 1){
             	 	throw new Exception('该手机号未注册，请先注册',1);
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
                    	if($this->save($data)){
                        	$user = Db::name('user')->field('id,mobile,names,status,password')->where($where)->find();
                    	}
                	}
                	 
             	 }
             }
            Session::set('user_id',$user['id']);
            Session::set('user_mobile', $user['mobile']);
            Session::set('user_name', $user['names']);
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


    /**
     * 修改密码
     * @param null $params
     * @throws \Exception
     */
     public function edit($params = null){
         try{
             if(empty($params)){
                 $params = $this->request->param();
             }
             $result = $this->validate($params,'modify');
             if($result !== true){
                 throw new Exception($result,1);
             }
             if(isset($params['type']) && $params['type'] == 2){
                 $pass = md5($params['pass']);
                 $uid = session('uid');
                 $password = md5($params['password']);
                 $user = Db('User')->where('id','=',$uid)->where('agent_class','=',1)->find();
             }else{
                 $uid = session('user_id');
                 $pass = md5($params['pass'] . Config::get('salt'));
                 $password = md5($params['password'] . Config::get('salt'));
                 $user = Db('User')->where('id','=',$uid)->where('agent_class','<>',1)->find();
             }
             if(!empty($user)){
                    if($user['mobile'] != $params['mobile']){
                        throw new Exception('手机号码错误',1);
                    }
                    if($user['password'] != $pass){
                        throw new Exception('原始密码错误',1);
                    }
                    $status = Db::name('user')->where('id',$user['id'])->update(['password'=>$password]);
                    if($status){
                        if($params['type'] == 2){
                            Session::delete('umobile');
                            Session::delete('ushare');
                            Session::delete('uname');
                            Session::delete('uid');
                        }else{
                            Session::delete('user_id');
                            Session::delete('user_mobile');
                            Session::delete('user_name');
                        }
                    }
             }
         }catch(\Exception $e){
             $this->log($e);
             throw $e;
         }
     }





}