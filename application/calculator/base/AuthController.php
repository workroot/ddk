<?php

namespace app\calculator\base;


use think\Loader;

class AuthController extends Controllers{

    public function _initialize()
    {
        parent::_initialize();
        $uid = session('cadmin_id');
        if(!isset($uid) || empty($uid)){
            $uid = "";
        }
        if($uid == null || $uid == "" || $uid == "null" || $uid == 0 || $uid == false){
            return $this->error('请登录！','/calculator/login/index', 1);
        }
        $this->assign('controller', Loader::parseName($this->request->controller()));
    }

}
