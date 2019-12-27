<?php
namespace app\backend\controller;

use app\backend\logic\LoginLogicQuery;
use app\common\base\Controllers;
use think\Config;
use think\Db;
use think\Session;

/**
 * 后台登录
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controllers
{
    /**
     * 后台登录
     * @return mixed
     */
    public function index()
    {
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
                LoginLogicQuery::getInstance()->login($data);
            }catch(\Exception $e){
               return $this->renderError($e);
            }
            return $this->renderSuccess('登录成功',['redirect'=>'/backend/index/index']);
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
        $this->success('退出成功', 'backend/login/index');
    }
}
