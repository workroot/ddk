<?php

namespace app\api\controller;


use app\api\logic\LoginLogicQuery;
use app\api\logic\SmsLogic;
use app\common\base\Controllers;

class Login extends Controllers
{


    /**
     * 添加用户
     * @return mixed|string
     */
    public function add()
    {
        try {
            $post = $this->request->param();
            $status = LoginLogicQuery::getInstance()->save($post);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->renderSuccess($status, ['redirect' => '/index/personal/index']);
    }


    /**
     * 用户登录
     * @return mixed|string
     */
    public function login()
    {
        try {
            $post = $this->request->param();
            $status = LoginLogicQuery::getInstance()->login($post);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->renderSuccess($status, ['redirect' => '/index/personal/index', 'status' => '1']);
    }


    /**
     * 发送验证码
     * @return null|string
     */
    public function sms()
    {
        try {
            $status = SmsLogic::getInstance()->sms();
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->renderSuccess($status);
    }


    public function edit()
    {
        try{
            $status = LoginLogicQuery::getInstance()->edit();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status);
    }


}