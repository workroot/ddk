<?php
namespace app\index\controller;

use app\common\base\Controllers;
use app\index\logic\LogoLogicQuery;
use think\Config;
use think\Session;

class Login extends Controllers{


    public function registered($pid=''){
    	$logo = LogoLogicQuery::getInstance()->logo();
    	$this->assign('logo',$logo);
        $this->assign('pid',$pid);
        return $this->fetch();
    }


    /**
     * 登录页面
     * @return mixed|string
     */
    public function login(){
        try{
            $logo = LogoLogicQuery::getInstance()->logo();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('logo',$logo);
        return $this->fetch();
    }


    /**
     * 退出
     */
    public function drop(){
        Session::delete('user_id');
        Session::delete('user_mobile');
        Session::delete('user_name');
        if (!session('user_name')) {
            $this->redirect('/index/login/login');
        }
    }


}