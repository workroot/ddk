<?php

namespace app\calculator\controller;

use app\calculator\base\Controllers;
use app\calculator\logic\LoginLogicQuery;
use think\Session;

class Login extends Controllers{

    /**
     * 添加管理员
     * @return mixed|string
     */
    public function save()
    {
        if ($this->request->isPost()) {
            try{
                $data = $this->request->param();
                $status = LoginLogicQuery::getInstance()->save($data);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/calculator/admin_user/index']);
        }
    }

    public function index(){
         return $this->fetch();
    }

    /**
     * 登录验证
     * @return mixed|string
     */
    public function login(){
        if($this->request->isPost()){
            try{
                $data = $this->request->only(['username', 'password', 'verify']);
                $a = LoginLogicQuery::getInstance()->login($data);
            }catch(\Exception $e){
            	         return $this->renderError($e);   
                //return $this->html_404($this->renderError($e));
            }
            return $this->renderSuccess('登录成功',['redirect'=>'/calculator/aindex/index']);
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Session::delete('admin_id');
        Session::delete('admin_name');
        Session::delete('names');
        $this->success('退出成功', '/calculator/login/index');
    }



}